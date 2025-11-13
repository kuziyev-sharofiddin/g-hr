import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface ApplicationTitle {
    id: number;
    name: string;
}

interface DeleteApplicationTitleDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title: ApplicationTitle | null;
}

export default function DeleteApplicationTitleDialog({
    open,
    onOpenChange,
    title,
}: DeleteApplicationTitleDialogProps) {
    const handleDelete = () => {
        if (!title) return;

        router.delete(`/documents/application-titles/${title.id}`, {
            onSuccess: () => {
                onOpenChange(false);
            },
        });
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Mavzuni o'chirishni tasdiqlang</DialogTitle>
                    <DialogDescription>
                        "{title?.name}" mavzusini o'chirishga haqiqatan ham rozisizmi?
                        <br />
                        Bu amalni bekor qilib bo'lmaydi.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                    >
                        Bekor qilish
                    </Button>
                    <Button type="button" variant="destructive" onClick={handleDelete}>
                        O'chirish
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
