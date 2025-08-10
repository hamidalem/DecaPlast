<template>
    <Modal :show="show" @close="$emit('close')">
        <template #header>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ajouter un nouveau fournisseur</h3>
        </template>
        <template #default>
            <form @submit.prevent="submitFournisseur">
                <div>
                    <label for="nom_fourn_modal" class="block text-sm font-medium text-gray-700">Nom du fournisseur</label>
                    <input
                        type="text"
                        id="nom_fourn_modal"
                        v-model="fournisseurForm.nom_fourn"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                </div>
                <!-- Added a new field for the phone number -->
                <div class="mt-4">
                    <label for="num_tel_fourn_modal" class="block text-sm font-medium text-gray-700">Numéro de téléphone</label>
                    <input
                        type="text"
                        id="num_tel_fourn_modal"
                        v-model="fournisseurForm.num_tel_fourn"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
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
import Modal from '@/Components/Modal.vue'; // Adjust the path as needed
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'newFournisseurAdded']);

const fournisseurForm = useForm({
    nom_fourn: '',
    num_tel_fourn: '', // Added this field
});

const submitFournisseur = () => {
    fournisseurForm.post('/fournisseurs', {
        onSuccess: () => {
            // Once saved, close the modal and emit an event to refresh the parent component's data
            emit('close');
            emit('newFournisseurAdded');
            fournisseurForm.reset();
        }
    });
};
</script>
