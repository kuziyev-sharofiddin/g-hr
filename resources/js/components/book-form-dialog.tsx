import { useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/input-error';

interface Book {
    id: number;
    name: string;
    short_description: string;
    long_description: string;
    book_author: {
        id: number;
        name: string;
    };
    book_genre: {
        id: number;
        name: string;
    };
    book_language: {
        id: number;
        name: string;
    };
    book_language_id: number;
    book_status: string;
    image_path: string | null;
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

interface BookFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    book?: Book | null;
    filterOptions: FilterOptions;
}

export default function BookFormDialog({
    open,
    onOpenChange,
    book,
    filterOptions,
}: BookFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm<{
        name: string;
        short_description: string;
        long_description: string;
        book_author_id: string;
        book_genre_id: string;
        book_language_id: string;
        book_status: string;
        image_path: string;
    }>({
        name: '',
        short_description: '',
        long_description: '',
        book_author_id: '',
        book_genre_id: '',
        book_language_id: '',
        book_status: 'all',
        image_path: '',
    });

    useEffect(() => {
        if (book) {
            setData({
                name: book.name,
                short_description: book.short_description,
                long_description: book.long_description,
                book_author_id: book.book_author.id.toString(),
                book_genre_id: book.book_genre.id.toString(),
                book_language_id: book.book_language_id.toString(),
                book_status: book.book_status || 'all',
                image_path: book.image_path || '',
            });
        } else {
            reset();
        }
    }, [book, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        const submitData = {
            name: data.name,
            short_description: data.short_description,
            long_description: data.long_description,
            book_author_id: parseInt(data.book_author_id),
            book_genre_id: parseInt(data.book_genre_id),
            book_language_id: parseInt(data.book_language_id),
            book_status: data.book_status,
            image_path: data.image_path,
        };

        if (book) {
            put(`/documents/books/${book.id}`, {
                data: submitData,
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                },
            });
        } else {
            post('/documents/books', {
                data: submitData,
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                },
            });
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[700px] max-h-[90vh] overflow-y-auto">
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {book ? 'Kitobni tahrirlash' : 'Yangi kitob qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {book
                                ? 'Kitob ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi kitob qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        {/* Basic Info */}
                        <div className="grid grid-cols-2 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="book_author_id">
                                    Muallif <span className="text-destructive">*</span>
                                </Label>
                                <Select
                                    value={data.book_author_id}
                                    onValueChange={(value) => setData('book_author_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Muallifni tanlang" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {filterOptions.authors.map((author) => (
                                            <SelectItem key={author.id} value={author.id.toString()}>
                                                {author.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.book_author_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="book_genre_id">
                                    Janr <span className="text-destructive">*</span>
                                </Label>
                                <Select
                                    value={data.book_genre_id}
                                    onValueChange={(value) => setData('book_genre_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Janrni tanlang" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {filterOptions.genres.map((genre) => (
                                            <SelectItem key={genre.id} value={genre.id.toString()}>
                                                {genre.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.book_genre_id} />
                            </div>
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="book_language_id">
                                    Til <span className="text-destructive">*</span>
                                </Label>
                                <Select
                                    value={data.book_language_id}
                                    onValueChange={(value) => setData('book_language_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Tilni tanlang" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {filterOptions.languages.map((language) => (
                                            <SelectItem key={language.id} value={language.id.toString()}>
                                                {language.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.book_language_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="book_status">Status</Label>
                                <Select
                                    value={data.book_status}
                                    onValueChange={(value) => setData('book_status', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Statusni tanlang" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">Barcha</SelectItem>
                                        <SelectItem value="unrecommended">Oddiy</SelectItem>
                                        <SelectItem value="recommended">Tavsiya qilingan</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.book_status} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="image_path">Rasm yo'li</Label>
                                <Input
                                    id="image_path"
                                    type="text"
                                    value={data.image_path}
                                    onChange={(e) => setData('image_path', e.target.value)}
                                    placeholder="https://example.com/image.jpg"
                                />
                                <InputError message={errors.image_path} />
                            </div>
                        </div>

                        {/* Book Details */}
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Kitob nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Kitob nomini kiriting"
                            />
                            <InputError message={errors.name} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="short_description">
                                Qisqa tavsif <span className="text-destructive">*</span>
                            </Label>
                            <Textarea
                                id="short_description"
                                value={data.short_description}
                                onChange={(e) => setData('short_description', e.target.value)}
                                placeholder="Kitob haqida qisqacha ma'lumot"
                                rows={2}
                            />
                            <InputError message={errors.short_description} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="long_description">
                                To'liq tavsif <span className="text-destructive">*</span>
                            </Label>
                            <Textarea
                                id="long_description"
                                value={data.long_description}
                                onChange={(e) => setData('long_description', e.target.value)}
                                placeholder="Kitob haqida to'liq ma'lumot"
                                rows={4}
                            />
                            <InputError message={errors.long_description} />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={processing}
                        >
                            Bekor qilish
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saqlanmoqda...' : book ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
