import PostFormModal from '@/components/post-form-modal';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { ImageWithTooltip } from '@/utils/ImageWithTooltip';
import { truncateWithTooltip } from '@/utils/truncateWithTooltip';
import { Head, router, usePage } from '@inertiajs/react';
import { ChevronDown, ChevronUp, Pencil, Search, Trash } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast, Toaster } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: '/posts',
    },
];

interface Post {
    id: number;
    title: string;
    slug: string;
    body: string;
    image?: string;
    created_at?: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    posts: {
        data: Post[];
        links: PaginationLink[];
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
    };
}

export default function Posts() {
    const { posts, filters } = usePage<PageProps>().props;
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [sortConfig, setSortConfig] = useState<{
        key: string;
        direction: 'asc' | 'desc';
    }>({
        key: 'id',
        direction: 'asc',
    });

    const handleSort = (key: 'id' | 'title' | 'created_at') => {
        setSortConfig((prev) => {
            const newDirection =
                prev.key === key && prev.direction === 'asc' ? 'desc' : 'asc';

            router.get(
                '/post',
                {
                    sort: key,
                    direction: newDirection,
                    search: searchTerm,
                    per_page: perPage,
                },
                { preserveState: true },
            );

            return { key, direction: newDirection };
        });
    };

    const [perPage, setPerPage] = useState(10); // default 10 ta post
    const handlePerPageChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
        const value = parseInt(e.target.value);
        setPerPage(value);

        // router.get orqali sahifa 1 bilan yuborish
        router.get(
            '/post',
            { per_page: value, search: searchTerm },
            { preserveState: true },
        );
    };

    // 🔍 LIVE SEARCH — foydalanuvchi yozishni to‘xtatgandan 2 soniya keyin qidiradi
    useEffect(() => {
        const delayDebounce = setTimeout(() => {
            router.get(
                '/post',
                { search: searchTerm, per_page: perPage },
                { preserveState: true, replace: true },
            );
        }, 2000); // 2 soniya kechikish

        return () => clearTimeout(delayDebounce); // typing davomida eski timeoutni tozalaydi
    }, [searchTerm, perPage]); // search yoki per_page o‘zgarganda qayta ishga tushadi

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedPost, setSelectedPost] = useState(null);

    const openModal = (post = null) => {
        setSelectedPost(post);
        setIsModalOpen(true);
    };

    const handleDelete = (id: number) => {
        router.delete(`/post/${id}`, {
            onSuccess: () => {
                toast.success('Post deleted successfully.');
                router.reload();
            },
            onError: () => {
                toast.error('Error deleting post.');
            },
        });
    };

    function handleDate(created_at: string) {
        if (!created_at) return '';
        const date = new Date(created_at);

        return new Intl.DateTimeFormat('en-GB', {
            day: '2-digit',
            month: 'short', // Oct
            year: 'numeric',
        }).format(date); // => 12 Oct 2025
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Posts" />
            <Toaster position="top-right" richColors />
            <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-transparent p-4 dark:border-sidebar-border">
                {/* Table Controls */}
                <div className="flex items-center justify-between border-b border-sidebar-border">
                    <div className="relative w-96">
                        <Search className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-500" />
                        <input
                            type="text"
                            placeholder="Search..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="text-dark w-full rounded-md border border-sidebar-border bg-transparent py-2 pr-4 pl-10 placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                    </div>

                    <div className="flex gap-2">
                        <div className="text-dark flex items-center gap-2 rounded-md border border-sidebar-border bg-transparent px-4 py-2 hover:bg-white/5">
                            {/*<span className="text-gray-500">Show:</span>*/}
                            <select
                                value={perPage}
                                onChange={handlePerPageChange}
                                className="rounded bg-none"
                            >
                                <option value={1}>1</option>
                                <option value={8}>8</option>
                                <option value={10}>10</option>
                                <option value={20}>20</option>
                                <option value={50}>50</option>
                            </select>
                        </div>

                        <button
                            className="text-dark flex items-center gap-2 rounded-md border border-sidebar-border bg-transparent px-4 py-2 hover:bg-white/5"
                            onClick={() => openModal()}
                        >
                            <span>+</span> Add Post
                        </button>
                    </div>
                </div>
                {/* Table */}
                <div className="mt-4 overflow-x-auto">
                    <table className="w-full">
                        <thead className="border-b border-sidebar-border">
                            <tr className="text-left text-sm text-gray-400">
                                <th className="w-12 p-4">
                                    <input
                                        type="checkbox"
                                        className="rounded border-gray-600 bg-transparent"
                                    />
                                </th>
                                <th
                                    className="cursor-pointer p-4"
                                    onClick={() => handleSort('id')}
                                >
                                    <div className="flex items-center gap-1">
                                        ID
                                        <div className="ml-1 flex flex-col">
                                            {sortConfig.key === 'id' ? (
                                                sortConfig.direction ===
                                                'asc' ? (
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </th>
                                <th className="hover:text-dark cursor-pointer p-4">
                                    <div className="flex items-center gap-2">
                                        Image
                                    </div>
                                </th>
                                <th
                                    className="cursor-pointer p-4"
                                    onClick={() => handleSort('title')}
                                >
                                    <div className="flex items-center gap-1">
                                        Title
                                        <div className="ml-1 flex flex-col">
                                            {sortConfig.key === 'title' ? (
                                                sortConfig.direction ===
                                                'asc' ? (
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </th>
                                <th className="hover:text-dark cursor-pointer p-4">
                                    <div className="flex items-center gap-2">
                                        Content
                                    </div>
                                </th>
                                <th
                                    className="cursor-pointer p-4"
                                    onClick={() => handleSort('created_at')}
                                >
                                    <div className="flex items-center gap-1">
                                        Date
                                        <div className="ml-1 flex flex-col">
                                            {sortConfig.key === 'created_at' ? (
                                                sortConfig.direction ===
                                                'asc' ? (
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                ) : (
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                )
                                            ) : (
                                                <>
                                                    <ChevronUp className="h-3 w-3 text-gray-500" />
                                                    <ChevronDown className="h-3 w-3 text-gray-500" />
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </th>
                                <th className="hover:text-dark cursor-pointer p-4">
                                    <div className="flex items-center gap-2">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {posts.data &&
                                posts.data.map((post) => (
                                    <tr
                                        key={post.id}
                                        className="text-dark border-b border-sidebar-border hover:bg-white/5"
                                    >
                                        <td className="p-4">
                                            <input
                                                type="checkbox"
                                                // checked={selectedRows.has(user.id)}
                                                // onChange={() => handleSelectRow(user.id)}
                                                className="rounded border-gray-600 bg-transparent"
                                            />
                                        </td>
                                        <td className="p-4">{post.id}</td>
                                        <td className="p-4">
                                            <ImageWithTooltip
                                                src={post.image}
                                                alt={post.title}
                                            />
                                        </td>
                                        <td className="p-4">
                                            {truncateWithTooltip(
                                                post.title,
                                                20,
                                            )}
                                        </td>
                                        <td className="p-4">
                                            {truncateWithTooltip(post.body, 50)}
                                        </td>
                                        <td className="p-4">
                                            {handleDate(post.created_at)}
                                        </td>
                                        <td className="flex items-center gap-2 p-4">
                                            <Button
                                                size="icon"
                                                variant="secondary"
                                                onClick={() => openModal(post)}
                                                className="h-8 w-8"
                                            >
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                size="icon"
                                                variant="destructive"
                                                onClick={() =>
                                                    handleDelete(post.id)
                                                }
                                                className="h-8 w-8"
                                            >
                                                <Trash className="h-4 w-4" />
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                        </tbody>
                    </table>
                </div>

                {/* Footer */}
                <div className="border-t border-sidebar-border bg-transparent p-4">
                    <div className="flex flex-col items-center justify-between gap-3 sm:flex-row">
                        {/* Info */}
                        <div className="flex items-center gap-2">
                            <div className="text-sm text-gray-500">
                                Showing{' '}
                                <span className="font-medium">
                                    {posts.from}
                                </span>
                                –<span className="font-medium">{posts.to}</span>{' '}
                                of{' '}
                                <span className="font-medium">
                                    {posts.total}
                                </span>{' '}
                                results
                            </div>
                        </div>

                        {/* Pagination Links */}
                        <div className="flex flex-wrap items-center gap-1.5">
                            {posts.links.map((link, index) => {
                                // Clean label (remove &laquo; and &raquo;)
                                const label =
                                    link.label === '&laquo; Previous'
                                        ? '←'
                                        : link.label === 'Next &raquo;'
                                          ? '→'
                                          : link.label;

                                // Handle "..." placeholder (non-clickable)
                                if (label === '...') {
                                    return (
                                        <span
                                            key={index}
                                            className="px-3 py-1.5 text-gray-400 select-none"
                                        >
                                            ...
                                        </span>
                                    );
                                }

                                return (
                                    <button
                                        key={index}
                                        disabled={!link.url}
                                        onClick={() =>
                                            link.url &&
                                            router.get(
                                                link.url,
                                                {},
                                                {
                                                    preserveState: true,
                                                    preserveScroll: true,
                                                },
                                            )
                                        }
                                        className={`min-w-[36px] rounded-lg px-3 py-1.5 text-sm font-medium transition-all duration-150 ${
                                            link.active
                                                ? 'bg-blue-600 text-white shadow-sm'
                                                : 'text-gray-700 hover:bg-blue-100 hover:text-blue-700'
                                        } ${!link.url ? 'cursor-not-allowed text-gray-400 opacity-50' : ''} `}
                                        dangerouslySetInnerHTML={{
                                            __html: label,
                                        }}
                                    />
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
            <PostFormModal
                isOpen={isModalOpen}
                closeModal={() => setIsModalOpen(false)}
                post={selectedPost}
            />
        </AppLayout>
    );
}
