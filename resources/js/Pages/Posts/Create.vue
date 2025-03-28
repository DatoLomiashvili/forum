<script setup>
import {useForm} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import MarkdownEditor from "@/Components/MarkdownEditor.vue";
import { isInProduction } from "@/Utilities/environment.js";

const form = useForm({
    title: '',
    body: '',
})

const createPost = () => form.post(route('posts.store'))

const autofill = async () => {
    if (isInProduction()){
        return;
    }
    const response = await axios.get('/local/post-content');

    form.title = response.data.title;
    form.body = response.data.body;
}

</script>

<template>
    <AppLayout title="Create a Post">
        <Container>
            <h1 class="text-2xl font-bold">Create a Post</h1>

            <form @submit.prevent="createPost" class="mt-6">
                <div>
                    <InputLabel for="title" class="sr-only">Title</InputLabel>
                    <TextInput id="title" class="w-full" v-model="form.title" placeholder="Give it a great title..." />
                    <InputError :message="form.errors.title" class="mt-1"/>
                </div>

                <div class="mt-3">
                    <InputLabel for="body" class="sr-only">Body</InputLabel>
                    <MarkdownEditor v-model="form.body">
                        <template #toolbar="{editor}">
                            <li v-if="! isInProduction()">
                                <button @click="autofill"
                                        type="button"
                                        class="px-3 py-2"
                                        title="AutoFill">
                                    <i class="ri-article-line"></i>
                                </button>
                            </li>
                        </template>
                    </MarkdownEditor>
                    <InputError :message="form.errors.body" class="mt-1"/>
                </div>

                <div class="mt-3">
                    <PrimaryButton type="submit">Create Post</PrimaryButton>
                </div>
            </form>
        </Container>
    </AppLayout>
</template>
