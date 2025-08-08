<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    produit: Object
});
</script>

<template>
    <Head title="Détails du Produit" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Détails du Produit</h1>
                <Link
                    href="/produits"
                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                    Retour à la liste
                </Link>
            </div>

            <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg max-w-2xl mx-auto p-8 border border-gray-200">
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">ID</p>
                        <p class="mt-1 text-sm text-gray-900">{{ produit.id_prod }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Nom</p>
                        <p class="mt-1 text-sm text-gray-900">{{ produit.nom_prod }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 text-sm text-gray-900">{{ produit.desc_prod || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Catégorie</p>
                        <p class="mt-1 text-sm text-gray-900">{{ produit.categorie?.nom_categ || 'Non catégorisé' }}</p>
                    </div>
                </div>

                <div class="flex justify-end mt-8">
                    <Link
                        :href="`/produits/${produit.id_prod}/edit`"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                    >
                        Modifier
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
