<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    nom_fourn: '',
    num_tel_fourn: '',
});
</script>

<template>
    <Head title="Create Fournisseur" />
    <AppLayout>
        <div class="container mx-auto p-4 sm:p-6 lg:p-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-6">Create Fournisseur</h1>

            <form @submit.prevent="form.post('/fournisseurs')" class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                <div class="mb-5">
                    <label for="nom_fourn" class="block text-sm font-medium text-gray-700 mb-2">Nom:</label>
                    <input
                        v-model="form.nom_fourn"
                        id="nom_fourn"
                        type="text"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.nom_fourn }"
                    >
                    <p v-if="form.errors.nom_fourn" class="mt-2 text-sm text-red-600">{{ form.errors.nom_fourn }}</p>
                </div>

                <div class="mb-8">
                    <label for="num_tel_fourn" class="block text-sm font-medium text-gray-700 mb-2">Téléphone:</label>
                    <input
                        v-model="form.num_tel_fourn"
                        id="num_tel_fourn"
                        type="text"
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                        :class="{ 'border-red-500': form.errors.num_tel_fourn }"
                    >
                    <p v-if="form.errors.num_tel_fourn" class="mt-2 text-sm text-red-600">{{ form.errors.num_tel_fourn }}</p>
                </div>

                <div class="flex justify-end space-x-4">
                    <Link
                        href="/fournisseurs"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors"
                        :disabled="form.processing"
                    >
                        Create
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
