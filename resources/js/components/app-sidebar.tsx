import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Database,
    FileText,
    Building2,
    Users,
    MessageSquare,
    ListTree,
    Award,
    BookText,
    User,
    Tags,
    BookMarked,
    Languages,
    Library,
    GraduationCap,
    Map,
    LogOut,
    ClipboardList,
    CheckCircle,
    Clock,
    AlertCircle,
    Send,
    BarChart3,
    HelpCircle,
    Lightbulb,
    Plus
} from 'lucide-react';

import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid
    },
    {
        title: 'Kitoblar',
        icon: BookText,
        items: [
            {
                title: 'Kitoblar',
                href: '/documents/books',
                icon: Library
            },
            {
                title: 'Kitob mualliflari',
                href: '/documents/book-authors',
                icon: User
            },
            {
                title: 'Kitob janr kategoriyalari',
                href: '/documents/book-genre-categories',
                icon: Tags
            },
            {
                title: 'Kitob janrlari',
                href: '/documents/book-genres',
                icon: BookMarked
            },
            {
                title: 'Kitob tillari',
                href: '/documents/book-languages',
                icon: Languages
            }
        ]
    },
    {
        title: 'Ma\'lumotlar',
        icon: Database,
        items: [
            {
                title: 'Bo\'limlar',
                href: '/documents/sections',
                icon: FileText
            },
            {
                title: 'Filiallar',
                href: '/documents/branches',
                icon: Building2
            },
            {
                title: 'Lavozimlar',
                href: '/documents/positions',
                icon: Award
            },
            {
                title: 'Shtatka qo\'shish',
                href: '/documents/shtatka',
                icon: Users
            },
            {
                title: 'Ariza mavzulari',
                href: '/documents/application-titles',
                icon: ClipboardList
            },
            {
                title: 'Xodim testi kategoriyalari',
                href: '/documents/question-for-worker-categories',
                icon: ListTree
            },
            {
                title: 'Xodimlar testi darajalari',
                href: '/documents/question-for-worker-levels',
                icon: Award
            },
            {
                title: 'Ishdan chiqarish sabablari',
                href: '/documents/dismissed-worker-reasons',
                icon: LogOut
            },
            {
                title: 'Taklif mavzulari',
                href: '/documents/suggestion-titles',
                icon: Lightbulb
            }
        ]
    },
    {
        title: 'O\'quv dasturi',
        icon: GraduationCap,
        href: dashboard()
    },
    {
        title: 'Yo\'riqnomalar',
        icon: Map,
        href: dashboard()
    },
    {
        title: 'Xodimlar',
        icon: Users,
        items: [
            {
                title: 'Xodimlar (HR-A)',
                href: '/worker/worker-hr-a',
                icon: Users
            },
            {
                title: 'Xodimlar',
                href: dashboard(),
                icon: Users
            },
            {
                title: 'Anketasiz xodimlarni qo\'shish',
                href: dashboard(),
                icon: Plus
            },
            {
                title: 'Filial o\'zgartirish',
                href: dashboard(),
                icon: Building2
            },
            {
                title: 'Lavozimga tayinlash',
                href: dashboard(),
                icon: Award
            },
            {
                title: 'Tatilga chiqarish',
                href: dashboard(),
                icon: Clock
            },
            {
                title: 'Tatilda bo\'lgan xodimlar',
                href: dashboard(),
                icon: Clock
            },
            {
                title: 'Ishdan bo\'shatish',
                href: dashboard(),
                icon: LogOut
            },
            {
                title: 'Qayta ishga olish',
                href: dashboard(),
                icon: CheckCircle
            },
            {
                title: 'Accountlar',
                href: dashboard(),
                icon: User
            }
        ]
    },
    {
        title: 'Davomat',
        icon: Clock,
        items: [
            {
                title: 'Davomat kiritish',
                href: dashboard(),
                icon: Clock
            },
            {
                title: 'Davomat jadvali',
                href: dashboard(),
                icon: ClipboardList
            }
        ]
    },
    {
        title: 'Recruiting',
        icon: Users,
        items: [
            {
                title: 'Bo\'sh ish o\'rinlari',
                href: dashboard(),
                icon: FileText
            },
            {
                title: 'Anketalar ro\'yxati',
                href: dashboard(),
                icon: ClipboardList
            },
            {
                title: 'Recruiting',
                href: dashboard(),
                icon: Users
            }
        ]
    },
    {
        title: 'Adaptatsiya',
        icon: CheckCircle,
        href: dashboard()
    },
    {
        title: 'Buyruqlar',
        icon: ClipboardList,
        items: [
            {
                title: 'Buyruq chiqarish',
                href: dashboard(),
                icon: FileText
            },
            {
                title: 'Ishga qabul qilinganlar',
                href: dashboard(),
                icon: CheckCircle
            },
            {
                title: 'Ishdan bo\'shganlar',
                href: dashboard(),
                icon: LogOut
            },
            {
                title: 'Filial o\'zgartirganlar',
                href: dashboard(),
                icon: Building2
            },
            {
                title: 'Tatil buyruqlari',
                href: dashboard(),
                icon: Clock
            }
        ]
    },
    {
        title: 'Testlar',
        icon: HelpCircle,
        items: [
            {
                title: 'Anketalar testi',
                href: dashboard(),
                icon: ClipboardList
            },
            {
                title: 'Xodimlar testlari',
                href: dashboard(),
                icon: Users
            }
        ]
    },
    {
        title: 'JSHSHIR',
        icon: AlertCircle,
        href: dashboard()
    },
    {
        title: 'Arizalar',
        icon: Send,
        href: dashboard()
    },
    {
        title: 'Takliflar',
        icon: Lightbulb,
        href: dashboard()
    },
    {
        title: 'Shikoyatlar',
        icon: AlertCircle,
        href: dashboard()
    },
    {
        title: 'Hisobotlar',
        icon: BarChart3,
        items: [
            {
                title: 'Reyting hisobti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Mobil dastur hisoboti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Ishdan bo\'shaganlar umumiy',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Ishdan bo\'shaganlar (xodimlar)',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Shtatkalar hisoboti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Anketalar hisoboti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Vazn hisoboti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Majburiy Bo\'limlar hisoboti',
                href: dashboard(),
                icon: BarChart3
            },
            {
                title: 'Xodim tug\'ilgan kunlari',
                href: dashboard(),
                icon: BarChart3
            }
        ]
    }
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/react-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#react',
    //     icon: BookOpen,
    // },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
