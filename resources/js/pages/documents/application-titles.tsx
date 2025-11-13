import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import {
    ChevronDown,
    ChevronUp,
    Ellipsis,
    Pencil,
    Plus,
    RotateCcw,
    Search,
    Trash,
    Trash2
} from 'lucide-react';
import ApplicationTitleFormDialog from '@/components/application-title-form-dialog';
import DeleteApplicationTitleDialog from '@/components/delete-application-title-dialog';
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
import { format } from 'date-fns';

interface ApplicationTitle {
    id: number;
    name: string;
    responsible_worker: string;
    created_at: string;
    deleted_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTitles {
    data: ApplicationTitle[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    titles: PaginatedTitles;
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
        title: 'Ariza mavzulari',
        href: '#'
    }
];

export default function ApplicationTitles({ titles, filters }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedTitle, setSelectedTitle] = useState<ApplicationTitle | null>(null);
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [selectionType, setSelectionType] = useState<'active' | 'deleted' | null>(null);
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc'
    });

    // Display all titles (active and deleted) together
    const displayedTitles = titles.data;

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/documents/application-titles',
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
                '/documents/application-titles',
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
            '/documents/application-titles',
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
        const totalPages = titles.last_page;
        const currentPage = titles.current_page;

        if (totalPages <= 5) {
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            pages.push(1);

            if (currentPage > 3) {
                pages.push('ellipsis');
            }

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

            if (!pages.includes(totalPages)) {
                pages.push(totalPages);
            }
        }

        return pages;
    };

    // Handle checkbox selection
    const handleSelectRow = (id: number, type: 'active' | 'deleted') => {
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
                    setSelectionType(type);
                    newSet.add(id);
                }
                // Only allow selection of same type
                else if ((selectionType === 'deleted' && type === 'deleted') || (selectionType === 'active' && type === 'active')) {
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
        // Get filterable titles based on current selection type
        const filterableTitles = selectionType === null
            ? displayedTitles.filter(t => !t.deleted_at) // Default to active
            : displayedTitles.filter(t => selectionType === 'deleted' ? t.deleted_at : !t.deleted_at);

        if (selectedRows.size === filterableTitles.length && selectedRows.size > 0) {
            // Deselect all
            setSelectedRows(new Set());
            setSelectionType(null);
        } else {
            // Select all of the same type
            const newType = selectionType || 'active';
            setSelectionType(newType);
            setSelectedRows(new Set(filterableTitles.map((t) => t.id)));
        }
    };

    // Handle bulk delete
    const handleBulkDelete = () => {
        if (selectedRows.size === 0) return;

        if (confirm(`${selectedRows.size} ta mavzuni o'chirmoqchimisiz?`)) {
            router.post(
                '/documents/application-titles/bulk-delete',
                { ids: Array.from(selectedRows) },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        setSelectedRows(new Set());
                        setSelectionType(null);
                    },
                }
            );
        }
    };

    // Handle bulk restore
    const handleBulkRestore = () => {
        if (selectedRows.size === 0) return;

        router.post(
            '/documents/application-titles/bulk-restore',
            { ids: Array.from(selectedRows) },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setSelectedRows(new Set());
                    setSelectionType(null);
                },
            }
        );
    };

    // Handle create button
    const handleCreate = () => {
        setSelectedTitle(null);
        setFormDialogOpen(true);
    };

    // Handle edit button
    const handleEdit = (title: ApplicationTitle) => {
        setSelectedTitle(title);
        setFormDialogOpen(true);
    };

    // Handle delete button
    const handleDelete = (title: ApplicationTitle) => {
        setSelectedTitle(title);
        setDeleteDialogOpen(true);
    };

    // Handle restore button
    const handleRestore = (title: ApplicationTitle) => {
        router.post(
            `/documents/application-titles/${title.id}/restore`,
            {},
            {
                preserveState: true,
                preserveScroll: true
            }
        );
    };

    // Handle double-click to show details
    const handleRowDoubleClick = (title: ApplicationTitle) => {
        setSelectedTitle(title);
        setFormDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Ariza mavzulari" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div
                    className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Ariza mavzulari ro'yhati</h2>
                    </div>

                    {/* Search and filters */}
                    <div className="flex flex-col gap-3">
                        {/* First row: Search on left, Bulk actions + Add button on right */}
                        <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div className="relative w-full md:w-[300px]">
                                <Search
                                    className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    placeholder="Qidirish..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    className="pl-9"
                                />
                            </div>

                            <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-end md:gap-2">
                                {/* Bulk actions */}
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

                                {/* Add button - always at the end */}
                                <Button className="gap-2" style={{ backgroundColor: '#27AE60' }} onClick={handleCreate}>
                                    Qo'shish
                                    <Plus className="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div
                    className="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[50px] text-center">
                                    <input
                                        type="checkbox"
                                        checked={
                                            selectedRows.size === displayedTitles.length &&
                                            displayedTitles.length > 0
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
                            {displayedTitles.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={5} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                displayedTitles.map((title, index) => (
                                    <TableRow
                                        key={title.id}
                                        className={`cursor-pointer ${title.deleted_at ? 'bg-destructive/5' : ''}`}
                                        onDoubleClick={() => handleRowDoubleClick(title)}
                                    >
                                        <TableCell className="text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedRows.has(title.id)}
                                                onChange={() =>
                                                    handleSelectRow(
                                                        title.id,
                                                        title.deleted_at ? 'deleted' : 'active'
                                                    )
                                                }
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </TableCell>
                                        <TableCell className="text-center font-medium">
                                            {(titles.current_page - 1) * titles.per_page + index + 1}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            <TruncateWithTooltip text={title.name} maxLength={30} />
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {format(new Date(title.created_at), 'dd.MM.yyyy')}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            <div className="flex justify-center gap-2">
                                                {!title.deleted_at ? (
                                                    <>
                                                        <Button
                                                            size="icon"
                                                            variant="secondary"
                                                            onClick={() => handleEdit(title)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Button>
                                                        <Button
                                                            size="icon"
                                                            variant="destructive"
                                                            onClick={() => handleDelete(title)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Trash className="h-4 w-4" />
                                                        </Button>
                                                    </>
                                                ) : (
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        onClick={() => handleRestore(title)}
                                                        className="h-8 w-8 border-green-500 text-green-600 hover:bg-green-500/10 dark:text-green-400"
                                                    >
                                                        <RotateCcw className="h-4 w-4" />
                                                    </Button>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>

                    {/* Pagination */}
                    {displayedTitles.length > 0 && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {titles.from}-{titles.to} / {titles.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    titles.current_page > 1 &&
                                                    handlePageChange(titles.current_page - 1)
                                                }
                                                className={
                                                    titles.current_page === 1
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
                                                        isActive={titles.current_page === page}
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
                                                    titles.current_page < titles.last_page &&
                                                    handlePageChange(titles.current_page + 1)
                                                }
                                                className={
                                                    titles.current_page === titles.last_page
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
            <ApplicationTitleFormDialog
                open={formDialogOpen}
                onOpenChange={setFormDialogOpen}
                title={selectedTitle}
            />
            <DeleteApplicationTitleDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                title={selectedTitle}
            />
        </AppLayout>
    );
}
