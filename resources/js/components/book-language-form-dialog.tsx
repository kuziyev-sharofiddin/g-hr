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

interface BookLanguage {
    id: number;
    name: string;
}

interface BookLanguageFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    language?: BookLanguage | null;
}

export default function BookLanguageFormDialog({
    open,
    onOpenChange,
    language,
}: BookLanguageFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
    });

    useEffect(() => {
        if (language) {
            setData({
                name: language.name,
            });
        } else {
            reset();
        }
    }, [language, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (language) {
            put(`/documents/book-languages/${language.id}`, {
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
            post('/documents/book-languages', {
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
                            {language ? 'Tilni tahrirlash' : 'Yangi til qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {language
                                ? 'Til ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi til qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Til nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: O'zbek tili"
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
                            {processing ? 'Saqlanmoqda...' : language ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
