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

interface QuestionForWorkerCategory {
    id: number;
    name: string;
    responsible_worker: string;
}

interface DeleteQuestionForWorkerCategoryDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    category: QuestionForWorkerCategory | null;
}

export default function DeleteQuestionForWorkerCategoryDialog({
    open,
    onOpenChange,
    category,
}: DeleteQuestionForWorkerCategoryDialogProps) {
    const { delete: destroy, processing } = useForm();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (category) {
            destroy(`/documents/question-for-worker-categories/${category.id}`, {
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
                        <DialogTitle>Savol kategoriyasini o'chirish</DialogTitle>
                        <DialogDescription>
                            Siz rostdan ham <strong>{category?.name}</strong> savol kategoriyasini o'chirmoqchimisiz? Bu amalni
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
