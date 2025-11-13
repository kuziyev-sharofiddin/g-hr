import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

interface Genre {
    id: number;
    name_uz: string;
    name_ru: string;
}

interface DeleteBookGenreDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    genre: Genre | null;
}

export default function DeleteBookGenreDialog({
    open,
    onOpenChange,
    genre,
}: DeleteBookGenreDialogProps) {
    const { delete: destroy, processing } = useForm();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (genre) {
            destroy(`/documents/book-genres/${genre.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    onOpenChange(false);
                },
            });
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <form onSubmit={submit}>
                    <DialogHeader>
                        <DialogTitle>Janrni o'chirish</DialogTitle>
                        <DialogDescription>
                            Siz rostdan ham <strong>{genre?.name_uz}</strong> janrni o'chirmoqchimisiz? Bu amalni
                            qaytarib bo'lmaydi.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter className="mt-4">
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => onOpenChange(false)}
                            disabled={processing}
                        >
                            Bekor qilish
                        </Button>
                        <Button
                            type="submit"
                            variant="destructive"
                            disabled={processing}
                        >
                            {processing ? 'O\'chirilmoqda...' : 'O\'chirish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
