<template>
    <Modal :show="show" @close="$emit('close')">
        <template #header>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Ajouter un nouveau client</h3>
        </template>
        <template #default>
            <form @submit.prevent="submitClient">
                <div>
                    <label for="nom_client_modal" class="block text-sm font-medium text-gray-700">Nom du client</label>
                    <input
                        type="text"
                        id="nom_client_modal"
                        v-model="clientForm.nom_client"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                </div>

                <div class="mt-4">
                    <label for="num_tel_client_modal" class="block text-sm font-medium text-gray-700">Numéro de téléphone</label>
                    <input
                        type="text"
                        id="num_tel_client_modal"
                        v-model="clientForm.num_tel_client"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >
                </div>

                <div class="mt-4">
                    <label for="adresse_client_modal" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input
                        type="text"
                        id="adresse_client_modal"
                        v-model="clientForm.adresse_client"
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
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close']);

const clientForm = useForm({
    nom_client: '',
    num_tel_client: '',
    adresse_client: '',
});

const submitClient = () => {
    clientForm.post('/clients', {
        onSuccess: () => {
            // Once saved, close the modal and refresh the parent component's data
            emit('close');
            clientForm.reset();
            // A simple page reload to fetch the new clients list is the quickest way to update
            window.location.reload();
        }
    });
};
</script>
