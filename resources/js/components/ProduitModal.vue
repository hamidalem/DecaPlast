<template>
    <Modal :show="show" @close="$emit('close')">
        <template #header>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ajouter un nouveau produit</h3>
        </template>
        <template #default>
            <form @submit.prevent="submitProduit">
                <div>
                    <label for="nom_prod_modal" class="block text-sm font-medium text-gray-700">Nom du produit</label>
                    <input
                        type="text"
                        id="nom_prod_modal"
                        v-model="produitForm.nom_prod"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                </div>

                <div class="mt-4">
                    <label for="desc_prod_modal" class="block text-sm font-medium text-gray-700">Description du produit</label>
                    <textarea
                        id="desc_prod_modal"
                        v-model="produitForm.desc_prod"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    ></textarea>
                </div>

                <div class="mt-4">
                    <label for="id_categ_modal" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select
                        id="id_categ_modal"
                        v-model="produitForm.id_categ"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                        <option value="">Sélectionner une catégorie</option>
                        <option v-for="categorie in categories" :key="categorie.id_categ" :value="categorie.id_categ">
                            {{ categorie.nom_categ }}
                        </option>
                    </select>
                </div>

                <div class="mt-4">
                    <button
                        type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900"
                    >
                        Enregistrer
                    </button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    categories: Array, // Correctly receives categories as a prop
});

const emit = defineEmits(['close', 'newProduitAdded']);

const produitForm = useForm({
    nom_prod: '',
    desc_prod: '',
    id_categ: '',
});

const submitProduit = () => {
    produitForm.post('/produits', {
        onSuccess: () => {
            emit('close');
            emit('newProduitAdded');
            produitForm.reset();
        }
    });
};
</script>
