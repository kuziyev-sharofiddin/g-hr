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
import SuggestionTitleFormDialog from '@/components/suggestion-title-form-dialog';
import DeleteSuggestionTitleDialog from '@/components/delete-suggestion-title-dialog';
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

interface Suggestion {
    id: number;
    code: string;
    name: string;
    responsible_worker: string;
    date: string;
    deleted_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedSuggestions {
    data: Suggestion[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    suggestions?: PaginatedSuggestions;
    filters?: {
        search?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hujjatlar',
        href: '#'
    },
    {
        title: 'Murojaat sabablari',
        href: '#'
    }
];

export default function SuggestionTitles({ suggestions, filters }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedSuggestionTitle, setSelectedSuggestionTitle] = useState<Suggestion | null>(null);
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [selectionType, setSelectionType] = useState<'active' | 'deleted' | null>(null);
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc'
    });

    // Filter suggestions into active and deleted
    const activeSuggestions = suggestions?.data?.filter((s) => !s.deleted_at) || [];
    const deletedSuggestions = suggestions?.data?.filter((s) => s.deleted_at) || [];
    const displayedSuggestions = activeSuggestions.length > 0 ? activeSuggestions : deletedSuggestions;

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/documents/suggestion-titles',
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
                '/documents/suggestion-titles',
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
            '/documents/suggestion-titles',
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
        const totalPages = suggestions?.last_page || 1;
        const currentPage = suggestions?.current_page || 1;

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
                newSet.delete(id);
            } else {
                newSet.add(id);
            }
            return newSet;
        });

        if (!selectionType) {
            setSelectionType(type);
        }
    };

    // Handle select all
    const handleSelectAll = () => {
        if (selectedRows.size === displayedSuggestions.length) {
            setSelectedRows(new Set());
            setSelectionType(null);
        } else {
            const type = activeSuggestions.length > 0 ? 'active' : 'deleted';
            setSelectedRows(new Set(displayedSuggestions.map((s) => s.id)));
            setSelectionType(type);
        }
    };

    // Handle bulk delete
    const handleBulkDelete = () => {
        if (selectedRows.size === 0) return;

        router.post(
            '/documents/suggestion-titles/bulk-delete',
            {
                ids: Array.from(selectedRows),
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    setSelectedRows(new Set());
                    setSelectionType(null);
                },
            }
        );
    };

    // Handle bulk restore
    const handleBulkRestore = () => {
        if (selectedRows.size === 0) return;

        router.post(
            '/documents/suggestion-titles/bulk-restore',
            {
                ids: Array.from(selectedRows),
            },
            {
                preserveState: true,
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
        setSelectedSuggestionTitle(null);
        setFormDialogOpen(true);
    };

    // Handle edit button
    const handleEdit = (suggestion: Suggestion) => {
        setSelectedSuggestionTitle(suggestion);
        setFormDialogOpen(true);
    };

    // Handle delete button
    const handleDelete = (suggestion: Suggestion) => {
        setSelectedSuggestionTitle(suggestion);
        setDeleteDialogOpen(true);
    };

    // Handle restore button
    const handleRestore = (suggestion: Suggestion) => {
        router.post(
            `/documents/suggestion-titles/${suggestion.id}/restore`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Murojaat sabablari" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Murojaat sabablari ro'yhati</h2>
                    </div>

                    {/* Search and filters */}
                    <div className="flex flex-col gap-3">
                        {/* First row: Search on left, Bulk actions + Add button on right */}
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

                            <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-end md:gap-2">
                                {/* Bulk actions */}
                                {selectedRows.size > 0 && (
                                    <>
                                        {selectionType === 'active' && (
                                            <Button
                                                size="sm"
                                                variant="destructive"
                                                className="gap-2"
                                                onClick={handleBulkDelete}
                                            >
                                                <Trash2 className="size-4" />
                                                O'chirish ({selectedRows.size})
                                            </Button>
                                        )}
                                        {selectionType === 'deleted' && (
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                className="gap-2"
                                                onClick={handleBulkRestore}
                                            >
                                                <RotateCcw className="size-4" />
                                                Tiklash ({selectedRows.size})
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
                <div className="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[50px] text-center">
                                    <input
                                        type="checkbox"
                                        checked={
                                            selectedRows.size === displayedSuggestions.length &&
                                            displayedSuggestions.length > 0
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
                                <TableHead className="min-w-[130px] text-center">Sana</TableHead>
                                <TableHead
                                    className="min-w-[200px] text-center cursor-pointer"
                                    onClick={() => handleSort('name')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Sabab
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
                                <TableHead className="min-w-[200px] text-center">Mas'ul xodim</TableHead>
                                <TableHead className="min-w-[120px] text-center">Amallar</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {displayedSuggestions.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                displayedSuggestions.map((suggestion, index) => (
                                    <TableRow
                                        key={suggestion.id}
                                        className={`cursor-pointer ${suggestion.deleted_at ? 'bg-destructive/5' : ''}`}
                                    >
                                        <TableCell className="text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedRows.has(suggestion.id)}
                                                onChange={() =>
                                                    handleSelectRow(
                                                        suggestion.id,
                                                        suggestion.deleted_at ? 'deleted' : 'active'
                                                    )
                                                }
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </TableCell>
                                        <TableCell className="text-center font-medium">
                                            {suggestion.code}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {suggestion.date}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {suggestion.name}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {suggestion.responsible_worker || '-'}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            <div className="flex justify-center gap-2">
                                                {!suggestion.deleted_at ? (
                                                    <>
                                                        <Button
                                                            size="icon"
                                                            variant="secondary"
                                                            onClick={() => handleEdit(suggestion)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Button>
                                                        <Button
                                                            size="icon"
                                                            variant="destructive"
                                                            onClick={() => handleDelete(suggestion)}
                                                            className="h-8 w-8"
                                                        >
                                                            <Trash className="h-4 w-4" />
                                                        </Button>
                                                    </>
                                                ) : (
                                                    <Button
                                                        size="icon"
                                                        variant="outline"
                                                        onClick={() => handleRestore(suggestion)}
                                                        className="h-8 w-8"
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
                    {displayedSuggestions.length > 0 && suggestions && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {suggestions.from}-{suggestions.to} / {suggestions.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    suggestions?.current_page > 1 &&
                                                    handlePageChange(suggestions.current_page - 1)
                                                }
                                                className={
                                                    suggestions?.current_page === 1
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
                                                        isActive={suggestions?.current_page === page}
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
                                                    suggestions?.current_page < suggestions?.last_page &&
                                                    handlePageChange(suggestions.current_page + 1)
                                                }
                                                className={
                                                    suggestions?.current_page === suggestions?.last_page
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
            <SuggestionTitleFormDialog
                open={formDialogOpen}
                onOpenChange={setFormDialogOpen}
                suggestionTitle={selectedSuggestionTitle}
            />
            <DeleteSuggestionTitleDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                suggestionTitle={selectedSuggestionTitle}
            />
        </AppLayout>
    );
}
