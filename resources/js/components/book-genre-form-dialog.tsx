import { useForm } from '@inertiajs/react';
import { FormEventHandler, useEffect, useState } from 'react';
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
import InputError from '@/components/input-error';
import { X, Check, ChevronsUpDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';

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
}

interface BookGenreFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    genre?: Genre | null;
    genreCategories: GenreCategory[];
}

export default function BookGenreFormDialog({
    open,
    onOpenChange,
    genre,
    genreCategories,
}: BookGenreFormDialogProps) {
    const [popoverOpen, setPopoverOpen] = useState(false);
    const [selectedCategories, setSelectedCategories] = useState<GenreCategory[]>([]);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        name_ru: '',
        genre_category_ids: [] as number[],
    });

    useEffect(() => {
        if (genre) {
            setData({
                name: genre.name,
                name_ru: genre.name_ru,
                genre_category_ids: genre.genre_categories.map((cat) => cat.id),
            });
            setSelectedCategories(genre.genre_categories);
        } else {
            reset();
            setSelectedCategories([]);
        }
    }, [genre, open]);

    const handleCategoryToggle = (category: GenreCategory) => {
        const isSelected = selectedCategories.some((cat) => cat.id === category.id);

        let newSelectedCategories: GenreCategory[];
        if (isSelected) {
            newSelectedCategories = selectedCategories.filter((cat) => cat.id !== category.id);
        } else {
            newSelectedCategories = [...selectedCategories, category];
        }

        setSelectedCategories(newSelectedCategories);
        setData('genre_category_ids', newSelectedCategories.map((cat) => cat.id));
    };

    const handleRemoveCategory = (categoryId: number) => {
        const newSelectedCategories = selectedCategories.filter((cat) => cat.id !== categoryId);
        setSelectedCategories(newSelectedCategories);
        setData('genre_category_ids', newSelectedCategories.map((cat) => cat.id));
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (genre) {
            put(`/documents/book-genres/${genre.id}`, {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                    setSelectedCategories([]);
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                },
            });
        } else {
            post('/documents/book-genres', {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    onOpenChange(false);
                    reset();
                    setSelectedCategories([]);
                },
                onError: (errors) => {
                    console.error('Validation errors:', errors);
                },
            });
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[550px]">
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {genre ? 'Janrni tahrirlash' : 'Yangi janr qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {genre
                                ? 'Janr ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi janr qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        {/* Genre Category Multi-Select */}
                        <div className="grid gap-2">
                            <Label htmlFor="genre_categories">
                                Janr kategoriyasi <span className="text-destructive">*</span>
                            </Label>
                            <Popover open={popoverOpen} onOpenChange={setPopoverOpen}>
                                <PopoverTrigger asChild>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        aria-expanded={popoverOpen}
                                        className="justify-between h-auto min-h-10 text-left font-normal"
                                    >
                                        <div className="flex flex-wrap gap-1">
                                            {selectedCategories.length === 0 ? (
                                                <span className="text-muted-foreground">Janr kategoriyasini tanlang</span>
                                            ) : (
                                                selectedCategories.map((category) => (
                                                    <span
                                                        key={category.id}
                                                        className="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-1 text-xs"
                                                    >
                                                        {category.name_uz}
                                                        <X
                                                            className="h-3 w-3 cursor-pointer hover:text-destructive"
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                handleRemoveCategory(category.id);
                                                            }}
                                                        />
                                                    </span>
                                                ))
                                            )}
                                        </div>
                                        <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent className="w-[500px] p-0" align="start">
                                    <Command>
                                        <CommandInput placeholder="Kategoriya qidirish..." />
                                        <CommandList>
                                            <CommandEmpty>Kategoriya topilmadi.</CommandEmpty>
                                            <CommandGroup>
                                                {genreCategories.map((category) => {
                                                    const isSelected = selectedCategories.some(
                                                        (cat) => cat.id === category.id
                                                    );
                                                    return (
                                                        <CommandItem
                                                            key={category.id}
                                                            value={`${category.name_uz} ${category.name_ru}`}
                                                            onSelect={() => handleCategoryToggle(category)}
                                                        >
                                                            <div
                                                                className={cn(
                                                                    'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                                                                    isSelected
                                                                        ? 'bg-primary text-primary-foreground'
                                                                        : 'opacity-50 [&_svg]:invisible'
                                                                )}
                                                            >
                                                                <Check className="h-4 w-4" />
                                                            </div>
                                                            <span>{category.name_uz}</span>
                                                        </CommandItem>
                                                    );
                                                })}
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                            <InputError message={errors.genre_category_ids} />
                        </div>

                        {/* Name */}
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Janr nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Badiiy adabiyot"
                            />
                            <InputError message={errors.name} />
                        </div>

                        {/* Name RU */}
                        <div className="grid gap-2">
                            <Label htmlFor="name_ru">
                                Janr nomi (rus tilida) <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name_ru"
                                type="text"
                                value={data.name_ru}
                                onChange={(e) => setData('name_ru', e.target.value)}
                                placeholder="Например: Художественная литература"
                            />
                            <InputError message={errors.name_ru} />
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
                            {processing ? 'Saqlanmoqda...' : genre ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
