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
import InputError from '@/components/input-error';

interface GenreCategory {
    id: number;
    name_uz: string;
    name_ru: string;
}

interface BookGenreCategoryFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    category?: GenreCategory | null;
}

export default function BookGenreCategoryFormDialog({
    open,
    onOpenChange,
    category,
}: BookGenreCategoryFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name_uz: '',
        name_ru: '',
    });

    useEffect(() => {
        if (category) {
            setData({
                name_uz: category.name_uz,
                name_ru: category.name_ru,
            });
        } else {
            reset();
        }
    }, [category, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (category) {
            put(`/documents/book-genre-categories/${category.id}`, {
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
            post('/documents/book-genre-categories', {
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
            <DialogContent className="sm:max-w-[500px]">
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>
                            {category ? 'Kategoriyani tahrirlash' : 'Yangi kategoriya qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {category
                                ? 'Kategoriya ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi kategoriya qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name_uz">
                                Kategoriya nomi (O'zbekcha) <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name_uz"
                                type="text"
                                value={data.name_uz}
                                onChange={(e) => setData('name_uz', e.target.value)}
                                placeholder="Masalan: Badiiy adabiyot"
                            />
                            <InputError message={errors.name_uz} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="name_ru">
                                Kategoriya nomi (Ruscha) <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name_ru"
                                type="text"
                                value={data.name_ru}
                                onChange={(e) => setData('name_ru', e.target.value)}
                                placeholder="Masalan: Художественная литература"
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
                            {processing ? 'Saqlanmoqda...' : category ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
