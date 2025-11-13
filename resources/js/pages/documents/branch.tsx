import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import {
    ChevronDown,
    ChevronUp,
    Ellipsis,
    MapPin,
    Pencil,
    Plus,
    RotateCcw,
    Search,
    Trash,
    Trash2
} from 'lucide-react';
import BranchFormDialog from '@/components/branch-form-dialog';
import DeleteBranchDialog from '@/components/delete-branch-dialog';
import { TruncateWithTooltip } from '@/utils/truncateWithTooltip';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { format } from 'date-fns';

interface Branch {
    id: number;
    name: string;
    address: string;
    phone_number: string;
    target: string;
    location: string;
    created_at: string;
    deleted_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBranches {
    data: Branch[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    branches: PaginatedBranches;
    filters: {
        search?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hujjatlar',
        href: '#'
    },
    {
        title: 'Filiallar',
        href: '#'
    }
];

export default function Branches({ branches, filters }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedBranch, setSelectedBranch] = useState<Branch | null>(null);
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [selectionType, setSelectionType] = useState<'active' | 'deleted' | null>(null);
    const [showTooltips, setShowTooltips] = useState(true);
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc'
    });

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/documents/branches',
                {
                    sort: key,
                    direction: newDirection,
                    search: searchTerm || undefined,
                },
                { preserveState: true, preserveScroll: true }
            );

            return { key, direction: newDirection };
        });
    };

    // Live search with debounce
    useEffect(() => {
        const delayDebounce = setTimeout(() => {
            router.get(
                '/documents/branches',
                {
                    search: searchTerm || undefined,
                },
                { preserveState: true, preserveScroll: true }
            );
        }, 300);

        return () => clearTimeout(delayDebounce);
    }, [searchTerm]);

    // Handle page change
    const handlePageChange = (page: number) => {
        router.get(
            '/documents/branches',
            {
                page,
                search: searchTerm || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    // Generate page numbers with ellipsis
    const generatePageNumbers = () => {
        const pages: (number | 'ellipsis')[] = [];
        const totalPages = branches.last_page;
        const currentPage = branches.current_page;

        if (totalPages <= 5) {
            // Show all pages if 5 or less
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);

            if (currentPage > 3) {
                pages.push('ellipsis');
            }

            // Show pages around current page
            const startPage = Math.max(2, currentPage - 1);
            const endPage = Math.min(totalPages - 1, currentPage + 1);

            for (let i = startPage; i <= endPage; i++) {
                if (!pages.includes(i)) {
                    pages.push(i);
                }
            }

            if (currentPage < totalPages - 2) {
                pages.push('ellipsis');
            }

            // Always show last page
            if (!pages.includes(totalPages)) {
                pages.push(totalPages);
            }
        }

        return pages;
    };

    // Handle checkbox selection
    const handleSelectRow = (id: number, isDeleted: boolean) => {
        setSelectedRows((prev) => {
            const newSet = new Set(prev);

            if (newSet.has(id)) {
                // Deselect
                newSet.delete(id);

                // If no more selections, reset selection type
                if (newSet.size === 0) {
                    setSelectionType(null);
                }
            } else {
                // First selection - set the type
                if (selectionType === null) {
                    setSelectionType(isDeleted ? 'deleted' : 'active');
                    newSet.add(id);
                }
                // Only allow selection of same type
                else if ((selectionType === 'deleted' && isDeleted) || (selectionType === 'active' && !isDeleted)) {
                    newSet.add(id);
                }
                // Different type - ignore
                else {
                    return prev;
                }
            }

            return newSet;
        });
    };

    // Handle select all
    const handleSelectAll = () => {
        // Get filterable branches based on current selection type
        const filterableBranches = selectionType === null
            ? branches.data.filter(b => !b.deleted_at) // Default to active
            : branches.data.filter(b => selectionType === 'deleted' ? b.deleted_at : !b.deleted_at);

        if (selectedRows.size === filterableBranches.length && selectedRows.size > 0) {
            // Deselect all
            setSelectedRows(new Set());
            setSelectionType(null);
        } else {
            // Select all of the same type
            const newType = selectionType || 'active';
            setSelectionType(newType);
            setSelectedRows(new Set(filterableBranches.map((b) => b.id)));
        }
    };

    // Handle create button
    const handleCreate = () => {
        setSelectedBranch(null);
        setFormDialogOpen(true);
    };

    // Handle edit button
    const handleEdit = (branch: Branch) => {
        setSelectedBranch(branch);
        setFormDialogOpen(true);
    };

    // Handle delete button
    const handleDelete = (branch: Branch) => {
        setSelectedBranch(branch);
        setDeleteDialogOpen(true);
    };

    // Handle bulk delete
    const handleBulkDelete = () => {
        if (selectedRows.size === 0) return;

        if (confirm(`${selectedRows.size} ta filialni o'chirmoqchimisiz?`)) {
            router.post(
                '/documents/branches/bulk-delete',
                { ids: Array.from(selectedRows) },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        setSelectedRows(new Set());
                        setSelectionType(null);
                    }
                }
            );
        }
    };

    // Handle bulk restore
    const handleBulkRestore = () => {
        if (selectedRows.size === 0) return;

        if (confirm(`${selectedRows.size} ta filialni tiklashni xohlaysizmi?`)) {
            router.post(
                '/documents/branches/bulk-restore',
                { ids: Array.from(selectedRows) },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        setSelectedRows(new Set());
                        setSelectionType(null);
                    }
                }
            );
        }
    };

    // Handle restore
    const handleRestore = (branchId: number) => {
        if (confirm('Bu filialni tiklashni xohlaysizmi?')) {
            router.post(
                `/documents/branches/${branchId}/restore`,
                {},
                {
                    preserveScroll: true
                }
            );
        }
    };

    // Handle double-click to show details
    const handleRowDoubleClick = (branch: Branch) => {
        if (branch.deleted_at) {
            // Don't open dialog for deleted branches
            return;
        }
        setSelectedBranch(branch);
        setFormDialogOpen(true);
    };

    // Open map
    const handleOpenMap = (location: string) => {
        if (!location) return;
        const [lat, lng] = location.split(',');
        const url = `https://www.google.com/maps?q=${lat},${lng}`;
        window.open(url, '_blank');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Filiallar" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Filiallar ro'yhati</h2>
                    </div>

                    {/* Search and action buttons */}
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div className="relative w-full md:w-[300px]">
                            <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                placeholder="Qidirish..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="pl-9"
                            />
                        </div>

                        <div className="flex gap-2 flex-wrap">
                            {/* Tooltip Toggle */}
                            <div className="flex items-center gap-2 rounded-md border border-sidebar-border bg-transparent px-4 py-2">
                                <label className="flex items-center gap-2 cursor-pointer text-sm">
                                    <input
                                        type="checkbox"
                                        checked={showTooltips}
                                        onChange={(e) => setShowTooltips(e.target.checked)}
                                        className="rounded border-gray-600 bg-transparent cursor-pointer"
                                    />
                                    <span>Tooltiplar</span>
                                </label>
                            </div>

                            {/* Bulk Action Buttons */}
                            {selectedRows.size > 0 && (
                                <>
                                    {selectionType === 'active' && (
                                        <Button
                                            variant="outline"
                                            className="gap-2 border-red-500 text-red-600 hover:bg-red-500/10 dark:text-red-400"
                                            onClick={handleBulkDelete}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                            {selectedRows.size} ta o'chirish
                                        </Button>
                                    )}
                                    {selectionType === 'deleted' && (
                                        <Button
                                            variant="outline"
                                            className="gap-2 border-green-500 text-green-600 hover:bg-green-500/10 dark:text-green-400"
                                            onClick={handleBulkRestore}
                                        >
                                            <RotateCcw className="h-4 w-4" />
                                            {selectedRows.size} ta tiklash
                                        </Button>
                                    )}
                                </>
                            )}

                            {/* Qo'shish button */}
                            <Button className="gap-2" style={{ backgroundColor: '#27AE60' }} onClick={handleCreate}>
                                Qo'shish
                                <Plus className="size-4" />
                            </Button>
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div className="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[50px] text-center">
                                    <input
                                        type="checkbox"
                                        checked={
                                            selectedRows.size === branches.data.length &&
                                            branches.data.length > 0
                                        }
                                        onChange={handleSelectAll}
                                        className="rounded border-gray-600 bg-transparent"
                                    />
                                </TableHead>
                                <TableHead
                                    className="w-[100px] text-center cursor-pointer"
                                    onClick={() => handleSort('id')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        №
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'id' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead
                                    className="min-w-[200px] text-center cursor-pointer"
                                    onClick={() => handleSort('name')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Nomi
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'name' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead className="min-w-[200px] text-center">Manzil</TableHead>
                                <TableHead className="min-w-[150px] text-center">Telefon</TableHead>
                                <TableHead className="min-w-[200px] text-center">Mo'ljal</TableHead>
                                <TableHead className="min-w-[100px] text-center">Joylashuv</TableHead>
                                <TableHead
                                    className="min-w-[130px] text-center cursor-pointer"
                                    onClick={() => handleSort('created_at')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Yaratilgan sana
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'created_at' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead className="min-w-[120px] text-center">Amallar</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {branches.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={9} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                branches.data.map((branch, index) => (
                                    <TableRow
                                        key={branch.id}
                                        className={branch.deleted_at ? 'bg-red-50 dark:bg-red-950/20' : 'cursor-pointer'}
                                        onDoubleClick={() => handleRowDoubleClick(branch)}
                                    >
                                        <TableCell className="text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedRows.has(branch.id)}
                                                onChange={() => handleSelectRow(branch.id, !!branch.deleted_at)}
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </TableCell>
                                        <TableCell className="text-center font-medium">
                                            {(branches.current_page - 1) * branches.per_page + index + 1}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {branch.name}
                                            {branch.deleted_at && (
                                                <span className="ml-2 text-xs text-red-600 dark:text-red-400">
                                                    (O'chirilgan)
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {showTooltips ? (
                                                <TruncateWithTooltip
                                                    text={branch.address || '-'}
                                                    maxLength={35}
                                                />
                                            ) : (
                                                <span className="block max-w-[200px] truncate">
                                                    {branch.address || '-'}
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {branch.phone_number || '-'}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {showTooltips ? (
                                                <TruncateWithTooltip
                                                    text={branch.target || '-'}
                                                    maxLength={30}
                                                />
                                            ) : (
                                                <span className="block max-w-[150px] truncate">
                                                    {branch.target || '-'}
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {branch.location ? (
                                                <div className="flex justify-center">
                                                    <div className="relative group">
                                                        <button
                                                            onClick={() => handleOpenMap(branch.location)}
                                                            className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                                        >
                                                            <MapPin className="h-4 w-4" />
                                                        </button>
                                                        {showTooltips && (
                                                            <>
                                                                {/* Tooltip pastga (birinchi qatorlar uchun) */}
                                                                {index < branches.data.length - 3 ? (
                                                                    <div className="absolute top-full left-1/2 -translate-x-1/2 mt-2 hidden group-hover:block z-50 pointer-events-none group-hover:pointer-events-auto">
                                                                        <div className="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl overflow-hidden">
                                                                            <div className="absolute bottom-full left-1/2 -translate-x-1/2 border-8 border-transparent border-b-white dark:border-b-gray-800"></div>
                                                                            <div className="p-2 bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                                                                                <p className="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                                    {branch.name}
                                                                                </p>
                                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                                    {branch.location}
                                                                                </p>
                                                                            </div>
                                                                            <iframe
                                                                                width="300"
                                                                                height="200"
                                                                                style={{ border: 0 }}
                                                                                loading="lazy"
                                                                                allowFullScreen
                                                                                src={`https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=${branch.location}&zoom=15`}
                                                                            ></iframe>
                                                                        </div>
                                                                    </div>
                                                                ) : (
                                                                    /* Tooltip yuqoriga (oxirgi 3 qator uchun) */
                                                                    <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-50 pointer-events-none group-hover:pointer-events-auto">
                                                                        <div className="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-xl overflow-hidden">
                                                                            <div className="p-2 bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                                                                                <p className="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                                                    {branch.name}
                                                                                </p>
                                                                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                                                                    {branch.location}
                                                                                </p>
                                                                            </div>
                                                                            <iframe
                                                                                width="300"
                                                                                height="200"
                                                                                style={{ border: 0 }}
                                                                                loading="lazy"
                                                                                allowFullScreen
                                                                                src={`https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=${branch.location}&zoom=15`}
                                                                            ></iframe>
                                                                            <div className="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-white dark:border-t-gray-800"></div>
                                                                        </div>
                                                                    </div>
                                                                )}
                                                            </>
                                                        )}
                                                    </div>
                                                </div>
                                            ) : (
                                                '-'
                                            )}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {format(new Date(branch.created_at), 'dd.MM.yyyy')}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            <div className="flex justify-center gap-2">
                                                {branch.deleted_at ? (
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        onClick={() => handleRestore(branch.id)}
                                                        className="h-8 w-8 border-green-500 text-green-600 hover:bg-green-500/10 dark:text-green-400"
                                                        title="Tiklash"
                                                    >
                                                        <RotateCcw className="h-4 w-4" />
                                                    </Button>
                                                ) : (
                                                    <>
                                                        <Button
                                                            size="icon"
                                                            variant="secondary"
                                                            onClick={() => handleEdit(branch)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Button>
                                                        <Button
                                                            size="icon"
                                                            variant="destructive"
                                                            onClick={() => handleDelete(branch)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Trash className="h-4 w-4" />
                                                        </Button>
                                                    </>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>

                    {/* Pagination */}
                    {branches.data.length > 0 && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {branches.from}-{branches.to} / {branches.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    branches.current_page > 1 &&
                                                    handlePageChange(branches.current_page - 1)
                                                }
                                                className={
                                                    branches.current_page === 1
                                                        ? 'pointer-events-none opacity-50'
                                                        : 'cursor-pointer'
                                                }
                                            />
                                        </PaginationItem>
                                        {generatePageNumbers().map((page, index) =>
                                            page === 'ellipsis' ? (
                                                <PaginationItem key={`ellipsis-${index}`}>
                                                    <span className="flex h-9 w-9 items-center justify-center">
                                                        <Ellipsis className="h-4 w-4" />
                                                    </span>
                                                </PaginationItem>
                                            ) : (
                                                <PaginationItem key={page}>
                                                    <PaginationLink
                                                        onClick={() => handlePageChange(page)}
                                                        isActive={branches.current_page === page}
                                                        className="cursor-pointer"
                                                    >
                                                        {page}
                                                    </PaginationLink>
                                                </PaginationItem>
                                            )
                                        )}
                                        <PaginationItem>
                                            <PaginationNext
                                                onClick={() =>
                                                    branches.current_page < branches.last_page &&
                                                    handlePageChange(branches.current_page + 1)
                                                }
                                                className={
                                                    branches.current_page === branches.last_page
                                                        ? 'pointer-events-none opacity-50'
                                                        : 'cursor-pointer'
                                                }
                                            />
                                        </PaginationItem>
                                    </PaginationContent>
                                </Pagination>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Dialogs */}
            <BranchFormDialog
                open={formDialogOpen}
                onOpenChange={setFormDialogOpen}
                branch={selectedBranch}
            />
            <DeleteBranchDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                branch={selectedBranch}
            />
        </AppLayout>
    );
}
