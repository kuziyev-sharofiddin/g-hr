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

interface BookAuthor {
    id: number;
    name: string;
    description?: string;
}

interface BookAuthorFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    author?: BookAuthor | null;
}

export default function BookAuthorFormDialog({
    open,
    onOpenChange,
    author,
}: BookAuthorFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        description: '',
    });

    useEffect(() => {
        if (author) {
            setData({
                name: author.name,
                description: author.description || '',
            });
        } else {
            reset();
        }
    }, [author, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (author) {
            put(`/documents/book-authors/${author.id}`, {
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
            post('/documents/book-authors', {
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
                            {author ? 'Muallifni tahrirlash' : 'Yangi muallif qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {author
                                ? 'Muallif ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi muallif qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Muallif ismi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Abdulla Qodiriy"
                            />
                            <InputError message={errors.name} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="description">
                                Izoh
                            </Label>
                            <Input
                                id="description"
                                type="text"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                placeholder="Muallif haqida qisqacha ma'lumot"
                            />
                            <InputError message={errors.description} />
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
                            {processing ? 'Saqlanmoqda...' : author ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
