import { useEffect, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import {
    ChevronDown,
    ChevronUp,
    Ellipsis,
    Search,
    X
} from 'lucide-react';
import { TruncateWithTooltip } from '@/utils/truncateWithTooltip';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow
} from '@/components/ui/table';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious
} from '@/components/ui/pagination';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { format } from 'date-fns';

interface Worker {
    id: number;
    name: string;
    phone_number: string;
    jshr_number: string;
    responsible_worker: string;
    created_at: string;
    deleted_at: string | null;
    branch?: {
        id: number;
        name: string;
    };
    position?: {
        id: number;
        name: string;
    };
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedWorkers {
    data: Worker[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Props {
    workers: PaginatedWorkers;
    filters: {
        search?: string;
        branch_id?: string;
        position_id?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Hujjatlar',
        href: '#'
    },
    {
        title: 'Xodimlar',
        href: '#'
    }
];

export default function WorkerHrA({ workers, filters }: Props) {
    if (!workers) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <div className="p-4 text-red-600">Error: Workers data not loaded</div>
            </AppLayout>
        );
    }

    const [searchTerm, setSearchTerm] = useState(filters?.search || '');
    const [branchId, setBranchId] = useState(filters?.branch_id || '');
    const [positionId, setPositionId] = useState(filters?.position_id || '');
    const [showTooltips, setShowTooltips] = useState(true);
    const [branches, setBranches] = useState<Array<{ id: number; name: string }>>([]);
    const [positions, setPositions] = useState<Array<{ id: number; name: string }>>([]);
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'created_at',
        direction: 'desc'
    });

    // Fetch branches and positions
    useEffect(() => {
        Promise.all([
            fetch('/for-filter-branch').then(res => res.json()),
            fetch('/for-filter-position').then(res => res.json())
        ])
        .then(([branches, positions]) => {
            setBranches(branches);
            setPositions(positions);
        });
    }, []);

    // Handle sorting
    const handleSort = (key: 'id' | 'name' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection = prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/worker/worker-hr-a',
                {
                    sort: key,
                    direction: newDirection,
                    search: searchTerm || undefined,
                    branch_id: branchId || undefined,
                    position_id: positionId || undefined
                },
                { preserveState: true, preserveScroll: true }
            );

            return { key, direction: newDirection };
        });
    };

    // Live search and filter with debounce
    useEffect(() => {
        const delayDebounce = setTimeout(() => {
            router.get(
                '/worker/worker-hr-a',
                {
                    search: searchTerm || undefined,
                    branch_id: branchId || undefined,
                    position_id: positionId || undefined
                },
                { preserveState: true, preserveScroll: true }
            );
        }, 300);

        return () => clearTimeout(delayDebounce);
    }, [searchTerm, branchId, positionId]);

    // Handle page change
    const handlePageChange = (page: number) => {
        router.get(
            '/worker/worker-hr-a',
            {
                page,
                search: searchTerm || undefined,
                branch_id: branchId || undefined,
                position_id: positionId || undefined
            },
            {
                preserveState: true,
                preserveScroll: true
            }
        );
    };

    // Generate page numbers with ellipsis
    const generatePageNumbers = () => {
        const pages: (number | 'ellipsis')[] = [];
        const totalPages = workers.last_page;
        const currentPage = workers.current_page;

        if (totalPages <= 5) {
            // Show all pages if 5 or less
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);

            if (currentPage > 3) {
                pages.push('ellipsis');
            }

            // Show pages around current page
            const startPage = Math.max(2, currentPage - 1);
            const endPage = Math.min(totalPages - 1, currentPage + 1);

            for (let i = startPage; i <= endPage; i++) {
                if (!pages.includes(i)) {
                    pages.push(i);
                }
            }

            if (currentPage < totalPages - 2) {
                pages.push('ellipsis');
            }

            // Always show last page
            if (!pages.includes(totalPages)) {
                pages.push(totalPages);
            }
        }

        return pages;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Xodimlar" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                {/* Header with filters and add button */}
                <div
                    className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
                    <div className="flex items-center justify-between">
                        <h2 className="text-2xl font-semibold">Xodimlar ro'yhati</h2>
                    </div>

                    {/* Search and filters - single row */}
                    <div className="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
                        {/* Left side - Search and filters */}
                        <div className="flex flex-col lg:flex-row gap-3 lg:items-center lg:flex-1">
                            {/* Search input */}
                            <div className="relative w-full lg:w-[280px]">
                                <Search className="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    placeholder="Qidirish..."
                                    value={searchTerm}
                                    onChange={(e) => setSearchTerm(e.target.value)}
                                    className="pl-9"
                                />
                            </div>

                            {/* Branch filter */}
                            <div className="w-full lg:w-[220px]">
                                <Select value={branchId || ''} onValueChange={(val) => setBranchId(val === '0' ? '' : val)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Filial" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">Barcha</SelectItem>
                                        {branches.map((branch) => (
                                            <SelectItem key={branch.id} value={String(branch.id)}>
                                                {branch.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Position filter */}
                            <div className="w-full lg:w-[220px]">
                                <Select value={positionId || ''} onValueChange={(val) => setPositionId(val === '0' ? '' : val)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Lavozim" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="0">Barcha</SelectItem>
                                        {positions.map((position) => (
                                            <SelectItem key={position.id} value={String(position.id)}>
                                                {position.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        {/* Right side - Tooltip Toggle */}
                        <div className="flex items-center gap-2 rounded-md border border-sidebar-border bg-transparent px-4 py-2 h-10 lg:ml-auto">
                            <label className="flex items-center gap-2 cursor-pointer text-sm whitespace-nowrap">
                                <input
                                    type="checkbox"
                                    checked={showTooltips}
                                    onChange={(e) => setShowTooltips(e.target.checked)}
                                    className="rounded border-gray-600 bg-transparent cursor-pointer"
                                />
                                <span>Tooltiplar</span>
                            </label>
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div
                    className="overflow-x-auto rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead
                                    className="w-[80px] text-center cursor-pointer"
                                    onClick={() => handleSort('id')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        No
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'id' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead
                                    className="min-w-[130px] text-center cursor-pointer"
                                    onClick={() => handleSort('created_at')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        Sana
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'created_at' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead
                                    className="min-w-[180px] text-center cursor-pointer"
                                    onClick={() => handleSort('name')}
                                >
                                    <div className="flex items-center justify-center gap-1">
                                        F.I.SH
                                        <div className="flex flex-col">
                                            {sortConfig.key === 'name' ? (
                                                sortConfig.direction === 'asc' ? (
                                                    <ChevronUp className="h-3 w-3" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-muted-foreground/50" />
                                                    <ChevronDown className="h-3 w-3 text-muted-foreground/50" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </TableHead>
                                <TableHead className="min-w-[150px] text-center">Filial nomi</TableHead>
                                <TableHead className="min-w-[130px] text-center">Lavozimi</TableHead>
                                <TableHead className="min-w-[130px] text-center">Tel</TableHead>
                                <TableHead className="min-w-[150px] text-center">JSHtSHIR (PINFL)</TableHead>
                                <TableHead className="min-w-[150px] text-center">Ma'sul xodim</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {workers.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={9} className="h-24 text-center">
                                        Ma'lumot topilmadi
                                    </TableCell>
                                </TableRow>
                            ) : (
                                workers.data.map((worker, index) => (
                                    <TableRow
                                        key={worker.id}
                                        className={worker.deleted_at ? 'bg-red-50 dark:bg-red-950/20' : 'hover:bg-muted/50'}
                                    >
                                        <TableCell className="text-center font-medium">
                                            {(workers.current_page - 1) * workers.per_page + index + 1}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center text-sm">
                                            {format(new Date(worker.created_at), 'dd.MM.yyyy HH:mm')}
                                        </TableCell>
                                        <TableCell className="text-center">
                                            {showTooltips ? (
                                                <TruncateWithTooltip
                                                    text={worker.name || '-'}
                                                    maxLength={25}
                                                />
                                            ) : (
                                                <span className="block max-w-[180px] truncate">
                                                    {worker.name || '-'}
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-center text-sm">
                                            {worker.branch?.name || '-'}
                                        </TableCell>
                                        <TableCell className="text-center text-sm">
                                            {worker.position?.name || '-'}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center text-sm">
                                            {worker.phone_number || '-'}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-center text-sm">
                                            {worker.jshr_number || '-'}
                                        </TableCell>
                                        <TableCell className="text-center text-sm">
                                            {showTooltips ? (
                                                <TruncateWithTooltip
                                                    text={worker.responsible_worker || '-'}
                                                    maxLength={20}
                                                />
                                            ) : (
                                                <span className="block max-w-[150px] truncate">
                                                    {worker.responsible_worker || '-'}
                                                </span>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>

                    {/* Pagination */}
                    {workers.data.length > 0 && (
                        <div className="flex items-center justify-between border-t border-sidebar-border/70 px-4 py-3">
                            <div className="text-sm text-muted-foreground">
                                {workers.from}-{workers.to} / {workers.total} ta natija
                            </div>
                            <div className="flex items-center gap-4">
                                <Pagination>
                                    <PaginationContent>
                                        <PaginationItem>
                                            <PaginationPrevious
                                                onClick={() =>
                                                    workers.current_page > 1 &&
                                                    handlePageChange(workers.current_page - 1)
                                                }
                                                className={
                                                    workers.current_page === 1
                                                        ? 'pointer-events-none opacity-50'
                                                        : 'cursor-pointer'
                                                }
                                            />
                                        </PaginationItem>
                                        {generatePageNumbers().map((page, index) =>
                                            page === 'ellipsis' ? (
                                                <PaginationItem key={`ellipsis-${index}`}>
                                                    <span className="flex h-9 w-9 items-center justify-center">
                                                        <Ellipsis className="h-4 w-4" />
                                                    </span>
                                                </PaginationItem>
                                            ) : (
                                                <PaginationItem key={page}>
                                                    <PaginationLink
                                                        onClick={() => handlePageChange(page)}
                                                        isActive={workers.current_page === page}
                                                        className="cursor-pointer"
                                                    >
                                                        {page}
                                                    </PaginationLink>
                                                </PaginationItem>
                                            )
                                        )}
                                        <PaginationItem>
                                            <PaginationNext
                                                onClick={() =>
                                                    workers.current_page < workers.last_page &&
                                                    handlePageChange(workers.current_page + 1)
                                                }
                                                className={
                                                    workers.current_page === workers.last_page
                                                        ? 'pointer-events-none opacity-50'
                                                        : 'cursor-pointer'
                                                }
                                            />
                                        </PaginationItem>
                                    </PaginationContent>
                                </Pagination>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
