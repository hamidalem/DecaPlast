<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    nom_categ: '',
});
</script>

<template>
    <Head title="Créer une Catégorie" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">
                Créer une Catégorie
            </h1>

            <form
                @submit.prevent="form.post('/categories')"
                class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200"
            >
                <div class="mb-5">
                    <label
                        for="nom_categ"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Nom de la Catégorie:
                    </label>
                    <input
                        v-model="form.nom_categ"
                        id="nom_categ"
                        type="text"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.nom_categ }"
                    >
                    <p
                        v-if="form.errors.nom_categ"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ form.errors.nom_categ }}
                    </p>
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
                        Créer
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
