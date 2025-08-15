<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    clients: Array
});
</script>

<template>
    <Head title="Tableau de Bord" />
    <AppLayout>
        <div class="flex flex-col items-center justify-center min-h-[calc(100vh-6rem)] p-10 text-center bg-gray-50">
            <img src="/logo.png" class="w-65 h-auto mb-8 animate-fade-in-down" alt="logo" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl">
                <!-- Card for Clients -->
                <Link href="/clients" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les clients</span>
                    </div>
                </Link>



                <!-- Card for Products -->
                <Link href="/produits" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800 mb-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les produits</span>
                    </div>
                </Link>

                <!-- Card for Categories -->
                <Link href="/categories" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 7V4a2 2 0 0 1 2-2h8.5L20 7.5V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z" />
                            <path d="M14 2v6h6" />
                            <path d="M10 12H7" />
                            <path d="M17 12h-3" />
                            <path d="M10 16H7" />
                            <path d="M17 16h-3" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les catégories</span>
                    </div>
                </Link>

                <!-- Card for Depots -->
                <Link href="/depots" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 text-gray-800 mb-4">
                            <path d="M12 2L2 7v14h20V7L12 2zM9 16H6v-6h3v6zM15 16h-3v-6h3v6z" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les dépôts</span>
                    </div>
                </Link>

                <!-- Card for Purchase Orders -->
                <Link href="/bon-achats" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                            <path d="M12 18v-6" />
                            <path d="M9 15h6" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les bons d'achat</span>
                    </div>
                </Link>

                <!-- Card for Sales Orders -->
                <Link href="/bon-ventes" class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:scale-105">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-800 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <path d="M14 2v6h6" />
                            <path d="M12 18V12" />
                            <path d="M16 16h-8" />
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Gérer les bons de vente</span>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<style>
@keyframes fade-in-down {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.8s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out 0.2s forwards;
}
</style>
