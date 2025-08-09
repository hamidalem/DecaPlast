<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    clients: Array
});
</script>

<template>
    <Head title="Accueil" />
    <AppLayout>
        <div class="flex flex-col items-center justify-center min-h-[calc(100vh-6rem)] p-10 text-center ">
            <img src="/logo.png " class="w-80 h-auto" alt="logo" />
            <p class="text-2xl text-gray-700 max-w-3xl mb-8">
                Votre partenaire de confiance en solutions plastiques de haute qualité. Nous proposons des produits innovants et durables pour diverses industries.
            </p>
            <Link href="/produits" class="px-8 py-4 text-xl font-semibold text-white bg-[#fd0057] rounded-full shadow-lg hover:bg-red-600 transition-colors duration-300">
                Découvrez nos produits
            </Link>
        </div>
    </AppLayout>
</template>
