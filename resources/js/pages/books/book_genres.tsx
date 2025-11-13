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
    Search,
    Trash,
    Trash2
} from 'lucide-react';
import BookGenreFormDialog from '@/components/book-genre-form-dialog';
import DeleteBookGenreDialog from '@/components/delete-book-genre-dialog';
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

interface GenreCategory {
    id: number;
    name_uz: string;
    name_ru: string;
}

interface Genre {
    id: number;
    name: string;
    name_ru: string;
    genre_categories: GenreCategory[];
    genre_categories_text: string;
    responsible_worker?: string;
    date: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedGenres {
    data: Genre[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    genres?: PaginatedGenres;
    genreCategories?: GenreCategory[];
    filters?: {
        search?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kitoblar',
        href: '#'
    },
    {
        title: 'Kitob janrlari',
        href: '#'
    }
];

export default function BookGenres({ genres, genreCategories, filters }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedGenre, setSelectedGenre] = useState<Genre | null>(null);
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc'
    });

    // Get all genres
    const displayedGenres = genres?.data || [];

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'name_ru' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/documents/book-genres',
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
                '/documents/book-genres',
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
            '/documents/book-genres',
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
        const totalPages = genres?.last_page || 1;
        const currentPage = genres?.current_page || 1;

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
    const handleSelectRow = (id: number) => {
        setSelectedRows((prev) => {
            const newSet = new Set(prev);
            if (newSet.has(id)) {
                newSet.delete(id);
            } else {
                newSet.add(id);
            }
            return newSet;
        });
    };

    // Handle select all
    const handleSelectAll = () => {
        if (selectedRows.size === displayedGenres.length) {
            setSelectedRows(new Set());
        } else {
            setSelectedRows(new Set(displayedGenres.map((s) => s.id)));
        }
    };

    // Handle bulk delete
    const handleBulkDelete = () => {
        if (selectedRows.size === 0) return;

        router.post(
            '/documents/book-genres/bulk-delete',
            {
                ids: Array.from(selectedRows),
            },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    setSelectedRows(new Set());
                },
            }
        );
    };

    // Handle create button
    const handleCreate = () => {
        setSelectedGenre(null);
        setFormDialogOpen(true);
    };

    // Handle edit button
    const handleEdit = (genre: Genre) => {
        setSelectedGenre(genre);
        setFormDialogOpen(true);
    };

    // Handle delete button
    const handleDelete = (genre: Genre) => {
        setSelectedGenre(genre);
        setDeleteDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kitob janrlari" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Kitob janrlari ro'yhati</h2>
                    </div>

                    {/* Search and filters */}
                    <div className="flex flex-col gap-3">
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

                                {/* Add button */}
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
                                            selectedRows.size === displayedGenres.length &&
                                            displayedGenres.length > 0
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
                                <TableHead className="min-w-[150px] text-center">Sana</TableHead>
                                <TableHead
                                    className="min-w-[200px] text-center cursor-pointer"
                                    onClick={() => handleSort('name')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Janr nomi
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
                                    className="min-w-[200px] text-center cursor-pointer"
                                    onClick={() => handleSort('name_ru')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Janr nomi (Ruscha)
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'name_ru' ? (
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
                                <TableHead className="min-w-[250px] text-center">Janr kategoriyalari</TableHead>
                                <TableHead className="min-w-[150px] text-center">Mas'ul xodim</TableHead>
                                <TableHead className="min-w-[120px] text-center">Amallar</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {displayedGenres.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                displayedGenres.map((genre, index) => (
                                    <TableRow
                                        key={genre.id}
                                        className="cursor-pointer"
                                    >
                                        <TableCell className="text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedRows.has(genre.id)}
                                                onChange={() => handleSelectRow(genre.id)}
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </TableCell>
                                        <TableCell className="text-center font-medium">
                                            {((genres?.current_page || 1) - 1) * (genres?.per_page || 20) + index + 1}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {genre.date}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {genre.name}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {genre.name_ru}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {genre.genre_categories_text || '-'}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {genre.responsible_worker || '-'}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            <div className="flex justify-center gap-2">
                                                <Button
                                                    size="icon"
                                                    variant="secondary"
                                                    onClick={() => handleEdit(genre)}
                                                    className="h-8 w-8"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    size="icon"
                                                    variant="destructive"
                                                    onClick={() => handleDelete(genre)}
                                                    className="h-8 w-8"
                                                >
                                                    <Trash className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>

                    {/* Pagination */}
                    {displayedGenres.length > 0 && genres && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {genres.from}-{genres.to} / {genres.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    genres?.current_page > 1 &&
                                                    handlePageChange(genres.current_page - 1)
                                                }
                                                className={
                                                    genres?.current_page === 1
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
                                                        isActive={genres?.current_page === page}
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
                                                    genres?.current_page < genres?.last_page &&
                                                    handlePageChange(genres.current_page + 1)
                                                }
                                                className={
                                                    genres?.current_page === genres?.last_page
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
            <BookGenreFormDialog
                open={formDialogOpen}
                onOpenChange={setFormDialogOpen}
                genre={selectedGenre}
                genreCategories={genreCategories || []}
            />
            <DeleteBookGenreDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                genre={selectedGenre}
            />
        </AppLayout>
    );
}
