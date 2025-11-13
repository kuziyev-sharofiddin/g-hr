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
import QuestionForWorkerCategoryFormDialog from '@/components/question-for-worker-category-form-dialog';
import DeleteQuestionForWorkerCategoryDialog from '@/components/delete-question-for-worker-category-dialog';
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

interface Category {
    id: number;
    code: string;
    name: string;
    responsible_worker: string;
    date: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedCategories {
    data: Category[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    categories?: PaginatedCategories;
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
        title: 'Xodimlar uchun test kategoriyalari',
        href: '#'
    }
];

export default function QuestionForWorkerCategories({ categories, filters }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState<Category | null>(null);
    const [selectedRows, setSelectedRows] = useState<Set<number>>(new Set());
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc'
    });

    // Get all categories
    const displayedCategories = categories?.data || [];

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/documents/question-for-worker-categories',
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
                '/documents/question-for-worker-categories',
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
            '/documents/question-for-worker-categories',
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
        const totalPages = categories?.last_page || 1;
        const currentPage = categories?.current_page || 1;

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
        if (selectedRows.size === displayedCategories.length) {
            setSelectedRows(new Set());
        } else {
            setSelectedRows(new Set(displayedCategories.map((s) => s.id)));
        }
    };

    // Handle bulk delete
    const handleBulkDelete = () => {
        if (selectedRows.size === 0) return;

        router.post(
            '/documents/question-for-worker-categories/bulk-delete',
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
        setSelectedCategory(null);
        setFormDialogOpen(true);
    };

    // Handle edit button
    const handleEdit = (category: Category) => {
        setSelectedCategory(category);
        setFormDialogOpen(true);
    };

    // Handle delete button
    const handleDelete = (category: Category) => {
        setSelectedCategory(category);
        setDeleteDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Xodimlar uchun test kategoriyalari" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Xodimlar uchun test kategoriyalari ro'yhati</h2>
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
                                            selectedRows.size === displayedCategories.length &&
                                            displayedCategories.length > 0
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
                                        Kategoriya nomi
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
                                <TableHead className="min-w-[120px] text-center">Amallar</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {displayedCategories.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={4} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                displayedCategories.map((category, index) => (
                                    <TableRow
                                        key={category.id}
                                        className="cursor-pointer"
                                    >
                                        <TableCell className="text-center">
                                            <input
                                                type="checkbox"
                                                checked={selectedRows.has(category.id)}
                                                onChange={() => handleSelectRow(category.id)}
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </TableCell>
                                        <TableCell className="text-center font-medium">
                                            {((categories?.current_page || 1) - 1) * (categories?.per_page || 20) + index + 1}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center">
                                            {category.name}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            <div className="flex justify-center gap-2">
                                                <Button
                                                    size="icon"
                                                    variant="secondary"
                                                    onClick={() => handleEdit(category)}
                                                    className="h-8 w-8"
                                                >
                                                    <Pencil className="h-4 w-4" />
                                                </Button>
                                                <Button
                                                    size="icon"
                                                    variant="destructive"
                                                    onClick={() => handleDelete(category)}
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
                    {displayedCategories.length > 0 && categories && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {categories.from}-{categories.to} / {categories.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    categories?.current_page > 1 &&
                                                    handlePageChange(categories.current_page - 1)
                                                }
                                                className={
                                                    categories?.current_page === 1
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
                                                        isActive={categories?.current_page === page}
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
                                                    categories?.current_page < categories?.last_page &&
                                                    handlePageChange(categories.current_page + 1)
                                                }
                                                className={
                                                    categories?.current_page === categories?.last_page
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
            <QuestionForWorkerCategoryFormDialog
                open={formDialogOpen}
                onOpenChange={setFormDialogOpen}
                category={selectedCategory}
            />
            <DeleteQuestionForWorkerCategoryDialog
                open={deleteDialogOpen}
                onOpenChange={setDeleteDialogOpen}
                category={selectedCategory}
            />
        </AppLayout>
    );
}
