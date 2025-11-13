import { useState, useEffect } from 'react';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface SectionsFilterModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    currentFilters: {
        search?: string;
        status?: string;
    };
    onApplyFilters: (filters: { search: string; status: string }) => void;
}

export default function SectionsFilterModal({
    open,
    onOpenChange,
    currentFilters,
    onApplyFilters,
}: SectionsFilterModalProps) {
    const [search, setSearch] = useState(currentFilters.search || '');
    const [status, setStatus] = useState(currentFilters.status || 'all');

    useEffect(() => {
        if (open) {
            setSearch(currentFilters.search || '');
            setStatus(currentFilters.status || 'all');
        }
    }, [open, currentFilters]);

    const handleApply = () => {
        onApplyFilters({ search, status });
        onOpenChange(false);
    };

    const handleReset = () => {
        setSearch('');
        setStatus('all');
        onApplyFilters({ search: '', status: 'all' });
        onOpenChange(false);
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Qidiruv va filter</DialogTitle>
                    <DialogDescription>
                        Bo'limlarni qidirish va filtrlash uchun parametrlarni
                        kiriting
                    </DialogDescription>
                </DialogHeader>

                <div className="grid gap-4 py-4">
                    {/* Search Input */}
                    <div className="grid gap-2">
                        <Label htmlFor="modal-search">Qidiruv</Label>
                        <Input
                            id="modal-search"
                            type="text"
                            placeholder="Bo'lim nomi yoki tavsifi..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>

                    {/* Status Filter */}
                    <div className="grid gap-2">
                        <Label htmlFor="modal-status">Status</Label>
                        <Select value={status} onValueChange={setStatus}>
                            <SelectTrigger id="modal-status">
                                <SelectValue placeholder="Status tanlang" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Barchasi</SelectItem>
                                <SelectItem value="active">Faol</SelectItem>
                                <SelectItem value="inactive">Nofaol</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        onClick={handleReset}
                    >
                        Tozalash
                    </Button>
                    <Button type="button" onClick={handleApply}>
                        Qo'llash
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
