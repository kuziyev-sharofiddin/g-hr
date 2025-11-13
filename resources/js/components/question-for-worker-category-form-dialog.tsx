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

interface QuestionForWorkerCategory {
    id: number;
    name: string;
    responsible_worker: string;
}

interface QuestionForWorkerCategoryFormDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    category?: QuestionForWorkerCategory | null;
}

export default function QuestionForWorkerCategoryFormDialog({
    open,
    onOpenChange,
    category,
}: QuestionForWorkerCategoryFormDialogProps) {
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
    });

    useEffect(() => {
        if (category) {
            setData({
                name: category.name,
            });
        } else {
            reset();
        }
    }, [category, open]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        if (category) {
            put(`/documents/question-for-worker-categories/${category.id}`, {
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
            post('/documents/question-for-worker-categories', {
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
                            {category ? 'Savol kategoriyasini tahrirlash' : 'Yangi savol kategoriyasi qo\'shish'}
                        </DialogTitle>
                        <DialogDescription>
                            {category
                                ? 'Savol kategoriyasi ma\'lumotlarini o\'zgartiring va saqlang'
                                : 'Yangi savol kategoriyasi qo\'shish uchun quyidagi ma\'lumotlarni kiriting'}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="grid gap-4 py-4">
                        <div className="grid gap-2">
                            <Label htmlFor="name">
                                Kategoriya nomi <span className="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Masalan: Ish jarayoni haqida"
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
                            {processing ? 'Saqlanmoqda...' : category ? 'Saqlash' : 'Qo\'shish'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
