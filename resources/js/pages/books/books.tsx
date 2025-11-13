import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Facebook, MessageCircle, Send } from 'lucide-react';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';

interface BookDetail {
    language_id: number;
    language_name: string;
    name: string;
    short_description: string;
    long_description: string;
}

interface Book {
    id: number;
    book_details: BookDetail[];
    book_author: {
        id: number;
        name: string;
    };
    book_language: Array<{
        id: number;
        name: string;
    }>;
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
    from: number;
    to: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
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

interface BooksResponse {
    data: Book[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    books: BooksResponse;
    filters: {
        search?: string;
        status?: string;
        feature?: string;
        filters?: {
            authors?: string;
            genres?: string;
        };
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

export default function Books({ books, filters, filterOptions }: Props) {
    const [selectedBooks, setSelectedBooks] = useState<number[]>([]);
    const [recommendedFilter, setRecommendedFilter] = useState(filters.status === 'recommended');
    const [selectedAuthor, setSelectedAuthor] = useState<string>(filters.filters?.authors || '');
    const [selectedGenre, setSelectedGenre] = useState<string>(filters.filters?.genres || '');
    const [sortFilter, setSortFilter] = useState<string>(filters.feature || '');

    const handleSelectAll = (checked: boolean) => {
        if (checked) {
            setSelectedBooks(books.data.map(book => book.id));
        } else {
            setSelectedBooks([]);
        }
    };

    const handleSelectBook = (bookId: number, checked: boolean) => {
        if (checked) {
            setSelectedBooks([...selectedBooks, bookId]);
        } else {
            setSelectedBooks(selectedBooks.filter(id => id !== bookId));
        }
    };

    const handleRecommendedChange = (checked: boolean) => {
        setRecommendedFilter(checked);
        applyFilters({ status: checked ? 'recommended' : 'all' });
    };

    const handleAuthorChange = (value: string) => {
        setSelectedAuthor(value);
        applyFilters({ authors: value });
    };

    const handleGenreChange = (value: string) => {
        setSelectedGenre(value);
        applyFilters({ genres: value });
    };

    const handleSortChange = (value: string) => {
        setSortFilter(value);
        applyFilters({ feature: value });
    };

    const applyFilters = (newFilters: any) => {
        const params: any = {};

        if (newFilters.status !== undefined) {
            if (newFilters.status !== 'all') params.status = newFilters.status;
        } else if (filters.status) {
            params.status = filters.status;
        }

        if (newFilters.feature !== undefined) {
            if (newFilters.feature) params.feature = newFilters.feature;
        } else if (sortFilter) {
            params.feature = sortFilter;
        }

        const filterParams: any = {};
        if (newFilters.authors !== undefined) {
            if (newFilters.authors) filterParams.authors = newFilters.authors;
        } else if (selectedAuthor) {
            filterParams.authors = selectedAuthor;
        }

        if (newFilters.genres !== undefined) {
            if (newFilters.genres) filterParams.genres = newFilters.genres;
        } else if (selectedGenre) {
            filterParams.genres = selectedGenre;
        }

        if (Object.keys(filterParams).length > 0) {
            params.filters = filterParams;
        }

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handlePageChange = (page: number) => {
        const params: any = { page };
        if (filters.status) params.status = filters.status;
        if (sortFilter) params.feature = sortFilter;
        if (selectedAuthor || selectedGenre) {
            params.filters = {
                ...(selectedAuthor && { authors: selectedAuthor }),
                ...(selectedGenre && { genres: selectedGenre }),
            };
        }

        router.get(route('documents.books'), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const shareOnFacebook = () => {
        const url = window.location.href;
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    };

    const shareOnWhatsApp = () => {
        const url = window.location.href;
        window.open(`https://wa.me/?text=${encodeURIComponent(url)}`, '_blank');
    };

    const shareOnTelegram = () => {
        const url = window.location.href;
        window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}`, '_blank');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kitoblar" />

            <div className="space-y-4">
                {/* Header with Social Share Buttons */}
                <div className="flex items-center gap-2">
                    <Button
                        size="sm"
                        className="bg-blue-600 hover:bg-blue-700"
                        onClick={shareOnFacebook}
                    >
                        <Facebook className="h-4 w-4" />
                    </Button>
                    <Button
                        size="sm"
                        className="bg-green-600 hover:bg-green-700"
                        onClick={shareOnWhatsApp}
                    >
                        <MessageCircle className="h-4 w-4" />
                    </Button>
                    <Button
                        size="sm"
                        className="bg-red-600 hover:bg-red-700"
                        onClick={shareOnTelegram}
                    >
                        <Send className="h-4 w-4" />
                    </Button>
                </div>

                {/* Filters */}
                <div className="flex items-center gap-4 flex-wrap">
                    {/* Recommended Checkbox */}
                    <div className="flex items-center space-x-2 border rounded-md px-3 py-2">
                        <Checkbox
                            id="recommended"
                            checked={recommendedFilter}
                            onCheckedChange={handleRecommendedChange}
                        />
                        <Label htmlFor="recommended" className="cursor-pointer text-sm">
                            Tavsiya qilingan
                        </Label>
                    </div>

                    {/* Sort Filter */}
                    <Select value={sortFilter} onValueChange={handleSortChange}>
                        <SelectTrigger className="w-[280px]">
                            <SelectValue placeholder="O'qilganlar (yuqorisidan boshlab)" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            <SelectItem value="read_desc">O'qilganlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="read_asc">O'qilganlar (quyisidan boshlab)</SelectItem>
                            <SelectItem value="rating_desc">Reyting (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="rating_asc">Reyting (quyisidan boshlab)</SelectItem>
                            <SelectItem value="liked_desc">Yoqtirganlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="liked_asc">Yoqtirganlar (quyisidan boshlab)</SelectItem>
                            <SelectItem value="comment_desc">Izohlar (yuqorisidan boshlab)</SelectItem>
                            <SelectItem value="comment_asc">Izohlar (quyisidan boshlab)</SelectItem>
                        </SelectContent>
                    </Select>

                    {/* Author Filter */}
                    <Select value={selectedAuthor} onValueChange={handleAuthorChange}>
                        <SelectTrigger className="w-[200px]">
                            <SelectValue placeholder="Muallif" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            {filterOptions.authors.map((author) => (
                                <SelectItem key={author.id} value={author.name}>
                                    {author.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    {/* Genre Filter */}
                    <Select value={selectedGenre} onValueChange={handleGenreChange}>
                        <SelectTrigger className="w-[200px]">
                            <SelectValue placeholder="Janrlar bo'yicha" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Barchasi</SelectItem>
                            {filterOptions.genres.map((genre) => (
                                <SelectItem key={genre.id} value={genre.name}>
                                    {genre.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {/* Table */}
                <div className="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-[50px]">
                                    <Checkbox
                                        checked={selectedBooks.length === books.data.length && books.data.length > 0}
                                        onCheckedChange={handleSelectAll}
                                    />
                                </TableHead>
                                <TableHead className="w-[60px]">№</TableHead>
                                <TableHead className="w-[100px]">Sana</TableHead>
                                <TableHead className="w-[250px]">Nomi</TableHead>
                                <TableHead>Qisqa ma'lumot</TableHead>
                                <TableHead className="w-[200px]">Kompaniya tasiya qilinishi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {books.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={6} className="text-center py-10 text-muted-foreground">
                                        Kitoblar topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                books.data.map((book, index) => {
                                    const bookDetail = book.book_details?.[0];
                                    return (
                                        <TableRow key={book.id}>
                                            <TableCell>
                                                <Checkbox
                                                    checked={selectedBooks.includes(book.id)}
                                                    onCheckedChange={(checked) => handleSelectBook(book.id, checked as boolean)}
                                                />
                                            </TableCell>
                                            <TableCell>{(books.current_page - 1) * books.per_page + index + 1}</TableCell>
                                            <TableCell>{book.created_at}</TableCell>
                                            <TableCell className="font-medium">{bookDetail?.name || '-'}</TableCell>
                                            <TableCell className="text-sm text-muted-foreground">
                                                {bookDetail?.short_description || '-'}
                                            </TableCell>
                                            <TableCell>
                                                {book.book_status === 'recommended' && (
                                                    <Badge className="bg-green-100 text-green-800 hover:bg-green-100 border border-green-300">
                                                        ✓ Tavsiya qilingan
                                                    </Badge>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })
                            )}
                        </TableBody>
                    </Table>
                </div>

                {/* Pagination */}
                {books.last_page > 1 && (
                    <div className="flex justify-center">
                        <Pagination>
                            <PaginationContent>
                                <PaginationItem>
                                    <PaginationPrevious
                                        onClick={() => books.current_page > 1 && handlePageChange(books.current_page - 1)}
                                        className={books.current_page === 1 ? 'pointer-events-none opacity-50' : 'cursor-pointer'}
                                    />
                                </PaginationItem>

                                {[...Array(books.last_page)].map((_, index) => {
                                    const pageNumber = index + 1;
                                    if (
                                        pageNumber === 1 ||
                                        pageNumber === books.last_page ||
                                        (pageNumber >= books.current_page - 1 && pageNumber <= books.current_page + 1)
                                    ) {
                                        return (
                                            <PaginationItem key={pageNumber}>
                                                <PaginationLink
                                                    onClick={() => handlePageChange(pageNumber)}
                                                    isActive={pageNumber === books.current_page}
                                                    className="cursor-pointer"
                                                >
                                                    {pageNumber}
                                                </PaginationLink>
                                            </PaginationItem>
                                        );
                                    }
                                    if (
                                        pageNumber === books.current_page - 2 ||
                                        pageNumber === books.current_page + 2
                                    ) {
                                        return <PaginationItem key={pageNumber}>...</PaginationItem>;
                                    }
                                    return null;
                                })}

                                <PaginationItem>
                                    <PaginationNext
                                        onClick={() => books.current_page < books.last_page && handlePageChange(books.current_page + 1)}
                                        className={books.current_page === books.last_page ? 'pointer-events-none opacity-50' : 'cursor-pointer'}
                                    />
                                </PaginationItem>
                            </PaginationContent>
                        </Pagination>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
