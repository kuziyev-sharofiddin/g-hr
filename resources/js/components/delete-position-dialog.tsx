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

interface Position {
    id: number;
    name: string;
    responsible_worker: string;
    section_id: number | null;
    does_it_belong_to_the_curator: boolean;
}

interface DeletePositionDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    position: Position | null;
}

export default function DeletePositionDialog({
    open,
    onOpenChange,
    position,
}: DeletePositionDialogProps) {
    const { delete: destroy, processing } = useForm();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (position) {
            destroy(`/documents/positions/${position.id}`, {
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
                        <DialogTitle>Lavozimni o'chirish</DialogTitle>
                        <DialogDescription>
                            Siz rostdan ham <strong>{position?.name}</strong> lavozimini o'chirmoqchimisiz? Bu amalni
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
