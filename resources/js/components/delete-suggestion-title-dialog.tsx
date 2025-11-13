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

interface SuggestionTitle {
    id: number;
    name: string;
    responsible_worker: string;
}

interface DeleteSuggestionTitleDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    suggestionTitle: SuggestionTitle | null;
}

export default function DeleteSuggestionTitleDialog({
    open,
    onOpenChange,
    suggestionTitle,
}: DeleteSuggestionTitleDialogProps) {
    const { delete: destroy, processing } = useForm();

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (suggestionTitle) {
            destroy(`/documents/suggestion-titles/${suggestionTitle.id}`, {
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
                        <DialogTitle>Murojaat sababini o'chirish</DialogTitle>
                        <DialogDescription>
                            Siz rostdan ham <strong>{suggestionTitle?.name}</strong> murojaat sababini o'chirmoqchimisiz? Bu amalni
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
