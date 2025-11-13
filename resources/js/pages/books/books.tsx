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
    BookOpen,
    Filter
} from 'lucide-react';
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
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import BookFormDialog from '@/components/book-form-dialog';

interface Book {
    id: number;
    name: string;
    short_description: string;
    long_description: string;
    book_author: {
        id: number;
        name: string;
    };
    book_language: {
        id: number;
        name: string;
    };
    book_language_id: number;
    book_genre: {
        id: number;
        name: string;
        name_ru: string;
    };
    book_status: string;
    responsible_worker: string;
    recommended_by_worker: string | null;
    image_path: string | null;
    likes: number;
    comments: number;
    ratings: number;
    read_workers: number;
    average_rating: number;
    created_at: string;
}

interface PaginationData {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Author {
    id: number;
    name: string;
}

interface Genre {
    id: number;
    name: string;
    name_ru: string;
}

interface Language {
    id: number;
    name: string;
}

interface FilterOptions {
    authors: Author[];
    genres: Genre[];
    languages: Language[];
}

interface Props {
    books: Book[];
    pagination: PaginationData;
    filters: {
        search?: string;
        recommended?: string;
        book_author_id?: number;
        book_genre_id?: number;
        book_language_id?: number;
        sort_filter?: string;
    };
    filterOptions: FilterOptions;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hujjatlar',
        href: '#'
    },
    {
        title: 'Kitoblar',
        href: '#'
    }
];

export default function Books({ books, pagination, filters, filterOptions }: Props) {
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [recommendedFilter, setRecommendedFilter] = useState(filters.recommended === 'true');
    const [selectedAuthor, setSelectedAuthor] = useState<string>(filters.book_author_id?.toString() || '');
    const [selectedGenre, setSelectedGenre] = useState<string>(filters.book_genre_id?.toString() || '');
    const [selectedLanguage, setSelectedLanguage] = useState<string>(filters.book_language_id?.toString() || '');
    const [sortFilter, setSortFilter] = useState<string>(filters.sort_filter || '');
    const [formDialogOpen, setFormDialogOpen] = useState(false);
    const [selectedBook, setSelectedBook] = useState<Book | null>(null);

    // Debounce search
    useEffect(() => {
        const timer = setTimeout(() => {
            applyFilters();
        }, 500);

        return () => clearTimeout(timer);
    }, [searchTerm]);

    const applyFilters = () => {
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleRecommendedChange = (checked: boolean) => {
        setRecommendedFilter(checked);
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (checked) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleAuthorChange = (value: string) => {
        setSelectedAuthor(value);
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (value) params.book_author_id = value;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleGenreChange = (value: string) => {
        setSelectedGenre(value);
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (value) params.book_genre_id = value;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleLanguageChange = (value: string) => {
        setSelectedLanguage(value);
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (value) params.book_language_id = value;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleSortChange = (value: string) => {
        setSortFilter(value);
        const params: any = {};
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (value) params.sort_filter = value;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const clearFilters = () => {
        setSearchTerm('');
        setRecommendedFilter(false);
        setSelectedAuthor('');
        setSelectedGenre('');
        setSelectedLanguage('');
        setSortFilter('');

        router.get(route('documents.books'), {}, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handlePageChange = (page: number) => {
        const params: any = { page };
        if (searchTerm) params.search = searchTerm;
        if (recommendedFilter) params.recommended = 'true';
        if (selectedAuthor) params.book_author_id = selectedAuthor;
        if (selectedGenre) params.book_genre_id = selectedGenre;
        if (selectedLanguage) params.book_language_id = selectedLanguage;
        if (sortFilter) params.sort_filter = sortFilter;

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kitoblar" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold">Kitoblar</h1>
                    <Button onClick={() => {
                        setSelectedBook(null);
                        setFormDialogOpen(true);
                    }}>
                        <Plus className="mr-2 h-4 w-4" />
                        Kitob qo'shish
                    </Button>
                </div>

                {/* Filters */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    {/* Search */}
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            placeholder="Qidirish..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="pl-9"
                        />
                    </div>

                    {/* Author Filter */}
                    <Select value={selectedAuthor} onValueChange={handleAuthorChange}>
                        <SelectTrigger>
                            <SelectValue placeholder="Muallif" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            {filterOptions.authors.map((author) => (
                                <SelectItem key={author.id} value={author.id.toString()}>
                                    {author.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    {/* Genre Filter */}
                    <Select value={selectedGenre} onValueChange={handleGenreChange}>
                        <SelectTrigger>
                            <SelectValue placeholder="Janrlar bo'yicha" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            {filterOptions.genres.map((genre) => (
                                <SelectItem key={genre.id} value={genre.id.toString()}>
                                    {genre.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    {/* Language Filter */}
                    <Select value={selectedLanguage} onValueChange={handleLanguageChange}>
                        <SelectTrigger>
                            <SelectValue placeholder="Til" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            {filterOptions.languages.map((language) => (
                                <SelectItem key={language.id} value={language.id.toString()}>
                                    {language.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    {/* Sort Filter */}
                    <Select value={sortFilter} onValueChange={handleSortChange}>
                        <SelectTrigger>
                            <SelectValue placeholder="Qo'shimcha filterlash" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Hech qanday</SelectItem>
                            <SelectItem value="readers_desc">O'qilganlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="readers_asc">O'qilganlar (quyisidan boshlab)</SelectItem>
                            <SelectItem value="rating_desc">Reyting (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="rating_asc">Reyting (quyisidan boshlab)</SelectItem>
                            <SelectItem value="likes_desc">Yoqtirganlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="likes_asc">Yoqtirganlar (quyisidan boshlab)</SelectItem>
                            <SelectItem value="comments_desc">Izohlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="comments_asc">Izohlar (quyisidan boshlab)</SelectItem>
                        </SelectContent>
                    </Select>

                    {/* Recommended Checkbox */}
                    <div className="flex items-center space-x-2">
                        <Checkbox
                            id="recommended"
                            checked={recommendedFilter}
                            onCheckedChange={handleRecommendedChange}
                        />
                        <Label htmlFor="recommended" className="cursor-pointer">
                            Tavsiya qilingan
                        </Label>
                    </div>
                </div>

                {/* Clear Filters Button */}
                {(searchTerm || recommendedFilter || selectedAuthor || selectedGenre || selectedLanguage || sortFilter) && (
                    <Button variant="outline" onClick={clearFilters}>
                        Tozalash
                    </Button>
                )}

                {/* Table */}
                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[50px]">№</TableHead>
                                <TableHead>Sana</TableHead>
                                <TableHead>Muallif</TableHead>
                                <TableHead>Kitob nomi</TableHead>
                                <TableHead>Janr</TableHead>
                                <TableHead>Tillar</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Mas'ul xodim</TableHead>
                                <TableHead className="text-center">O'qilgan</TableHead>
                                <TableHead className="text-center">Reyting</TableHead>
                                <TableHead className="text-center">Izohlar</TableHead>
                                <TableHead className="w-[70px]"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {books.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={12} className="text-center py-10 text-muted-foreground">
                                        Kitoblar topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                books.map((book, index) => (
                                    <TableRow key={book.id}>
                                        <TableCell>{(pagination.current_page - 1) * pagination.per_page + index + 1}</TableCell>
                                        <TableCell>{book.created_at}</TableCell>
                                        <TableCell>{book.book_author.name}</TableCell>
                                        <TableCell>{book.name}</TableCell>
                                        <TableCell>{book.book_genre.name}</TableCell>
                                        <TableCell>
                                            <Badge variant="outline" className="text-xs">
                                                {book.book_language?.name || '-'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {book.book_status === 'recommended' ? (
                                                <Badge variant="default">Tavsiya qilingan</Badge>
                                            ) : (
                                                <Badge variant="secondary">Oddiy</Badge>
                                            )}
                                        </TableCell>
                                        <TableCell>{book.responsible_worker}</TableCell>
                                        <TableCell className="text-center">{book.read_workers}</TableCell>
                                        <TableCell className="text-center">
                                            {book.average_rating > 0 ? book.average_rating.toFixed(1) : '-'}
                                        </TableCell>
                                        <TableCell className="text-center">{book.comments}</TableCell>
                                        <TableCell>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button variant="ghost" size="icon">
                                                        <Ellipsis className="h-4 w-4" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuItem onClick={() => {
                                                        setSelectedBook(book);
                                                        setFormDialogOpen(true);
                                                    }}>
                                                        <Pencil className="mr-2 h-4 w-4" />
                                                        Tahrirlash
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem
                                                        className="text-destructive"
                                                        onClick={() => {
                                                            if (confirm('Kitobni o\'chirishni xohlaysizmi?')) {
                                                                router.delete(route('documents.books.destroy', book.id));
                                                            }
                                                        }}
                                                    >
                                                        <Trash className="mr-2 h-4 w-4" />
                                                        O'chirish
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>

                {/* Pagination */}
                {pagination.last_page > 1 && (
                    <Pagination>
                        <PaginationContent>
                            <PaginationItem>
                                <PaginationPrevious
                                    onClick={() => pagination.current_page > 1 && handlePageChange(pagination.current_page - 1)}
                                    className={pagination.current_page === 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'}
                                />
                            </PaginationItem>

                            {[...Array(pagination.last_page)].map((_, index) => {
                                const pageNumber = index + 1;
                                // Show first page, last page, current page and pages around it
                                if (
                                    pageNumber === 1 ||
                                    pageNumber === pagination.last_page ||
                                    (pageNumber >= pagination.current_page - 1 && pageNumber <= pagination.current_page + 1)
                                ) {
                                    return (
                                        <PaginationItem key={pageNumber}>
                                            <PaginationLink
                                                onClick={() => handlePageChange(pageNumber)}
                                                isActive={pageNumber === pagination.current_page}
                                                className="cursor-pointer"
                                            >
                                                {pageNumber}
                                            </PaginationLink>
                                        </PaginationItem>
                                    );
                                }
                                // Show ellipsis
                                if (
                                    pageNumber === pagination.current_page - 2 ||
                                    pageNumber === pagination.current_page + 2
                                ) {
                                    return <PaginationItem key={pageNumber}>...</PaginationItem>;
                                }
                                return null;
                            })}

                            <PaginationItem>
                                <PaginationNext
                                    onClick={() => pagination.current_page < pagination.last_page && handlePageChange(pagination.current_page + 1)}
                                    className={pagination.current_page === pagination.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'}
                                />
                            </PaginationItem>
                        </PaginationContent>
                    </Pagination>
                )}

                {/* Book Form Dialog */}
                <BookFormDialog
                    open={formDialogOpen}
                    onOpenChange={setFormDialogOpen}
                    book={selectedBook}
                    filterOptions={filterOptions}
                />
            </div>
        </AppLayout>
    );
}
