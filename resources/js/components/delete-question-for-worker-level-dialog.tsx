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

interface QuestionForWorkerLevel {
    id: number;
    name: string;
    responsible_worker: string;
}

interface DeleteQuestionForWorkerLevelDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    level: QuestionForWorkerLevel | null;
}

export default function DeleteQuestionForWorkerLevelDialog({
    open,
    onOpenChange,
    level,
}: DeleteQuestionForWorkerLevelDialogProps) {
    const { delete: destroy, processing } = useForm();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (level) {
            destroy(`/documents/question-for-worker-levels/${level.id}`, {
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
                        <DialogTitle>Darajani o'chirish</DialogTitle>
                        <DialogDescription>
                            Siz rostdan ham <strong>{level?.name}</strong> darajani o'chirmoqchimisiz? Bu amalni
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
