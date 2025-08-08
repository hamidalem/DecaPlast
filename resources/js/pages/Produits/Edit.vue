<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    produit: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        required: true
    }
});

const form = useForm({
    nom_prod: props.produit.nom_prod,
    desc_prod: props.produit.desc_prod,
    id_categ: props.produit.id_categ
});
</script>

<template>
    <Head title="Modifier le Produit" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">Modifier le Produit</h1>

            <form @submit.prevent="form.put(`/produits/${props.produit.id_prod}`)" class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <div class="mb-5">
                    <label for="nom_prod" class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit:</label>
                    <input
                        v-model="form.nom_prod"
                        id="nom_prod"
                        type="text"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.nom_prod }"
                    >
                    <p v-if="form.errors.nom_prod" class="mt-2 text-sm text-red-600">{{ form.errors.nom_prod }}</p>
                </div>

                <div class="mb-5">
                    <label for="desc_prod" class="block text-sm font-medium text-gray-700 mb-2">Description:</label>
                    <textarea
                        v-model="form.desc_prod"
                        id="desc_prod"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.desc_prod }"
                        rows="4"
                    ></textarea>
                    <p v-if="form.errors.desc_prod" class="mt-2 text-sm text-red-600">{{ form.errors.desc_prod }}</p>
                </div>

                <div class="mb-8">
                    <label for="id_categ" class="block text-sm font-medium text-gray-700 mb-2">Catégorie:</label>
                    <select
                        v-model="form.id_categ"
                        id="id_categ"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.id_categ }"
                    >
                        <option :value="null">Non catégorisé</option>
                        <option
                            v-for="categorie in categories"
                            :key="categorie.id_categ"
                            :value="categorie.id_categ"
                        >
                            {{ categorie.nom_categ }}
                        </option>
                    </select>
                    <p v-if="form.errors.id_categ" class="mt-2 text-sm text-red-600">{{ form.errors.id_categ }}</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <Link
                        href="/produits"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Annuler
                    </Link>
                    <button
                        type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                        :disabled="form.processing"
                    >
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
