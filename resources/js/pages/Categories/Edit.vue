<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    categorie: Object,
    parentCategories: Array
});

const form = useForm({
    nom_categ: props.categorie.nom_categ,
    id_parent: props.categorie.id_parent,
});
</script>

<template>
    <Head title="Modifier la Catégorie" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">Modifier la Catégorie</h1>

            <form @submit.prevent="form.put(`/categories/${props.categorie.id_categ}`)"
                  class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <div class="mb-5">
                    <label for="nom_categ" class="block text-sm font-medium text-gray-700 mb-2">Nom de la
                        Catégorie:</label>
                    <input
                        v-model="form.nom_categ"
                        id="nom_categ"
                        type="text"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.nom_categ }"
                    >
                    <p v-if="form.errors.nom_categ" class="mt-2 text-sm text-red-600">{{ form.errors.nom_categ }}</p>
                </div>

                <div class="mb-8">
                    <label for="id_parent" class="block text-sm font-medium text-gray-700 mb-2">Catégorie
                        Parente:</label>
                    <select
                        v-model="form.id_parent"
                        id="id_parent"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.id_parent }"
                    >
                        <option :value="null">Aucune (Catégorie Principale)</option>
                        <option
                            v-for="parent in parentCategories"
                            :key="parent.id_categ"
                            :value="parent.id_categ"
                            :disabled="parent.id_categ === categorie.id_categ"
                        >
                            {{ parent.nom_categ }}
                        </option>
                    </select>
                    <p v-if="form.errors.id_parent" class="mt-2 text-sm text-red-600">{{ form.errors.id_parent }}</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <Link
                        href="/categories"
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
