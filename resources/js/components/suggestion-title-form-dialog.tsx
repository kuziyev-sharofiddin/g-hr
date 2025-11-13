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

interface SuggestionTitle {
    id: number;
    name: string;
    responsible_worker: string;
}

interface SuggestionTitleFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    suggestionTitle?: SuggestionTitle | null;
}

export default function SuggestionTitleFormDialog({
    open,
    onOpenChange,
    suggestionTitle,
}: SuggestionTitleFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
    });

    useEffect(() => {
        if (suggestionTitle) {
            setData({
                name: suggestionTitle.name,
            });
        } else {
            reset();
        }
    }, [suggestionTitle, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (suggestionTitle) {
            put(`/documents/suggestion-titles/${suggestionTitle.id}`, {
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
            post('/documents/suggestion-titles', {
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
                            {suggestionTitle ? 'Murojaat sababini tahrirlash' : 'Yangi murojaat sababi qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {suggestionTitle
                                ? 'Murojaat sababi ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi murojaat sababi qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Sabab nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Ish haqi haqida"
                            />
                            <InputError message={errors.name} />
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
                            {processing ? 'Saqlanmoqda...' : suggestionTitle ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
