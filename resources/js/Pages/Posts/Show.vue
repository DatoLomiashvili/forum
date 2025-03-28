<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import {relativeDate} from "@/Utilities/date.js";
import Comment from "@/Components/Comment.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {router, useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import {computed, ref} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useConfirm} from "@/Utilities/Composables/useConfirm.js";
import MarkdownEditor from "@/Components/MarkdownEditor.vue";


const props = defineProps(['post', 'comments'])

const formattedDate = relativeDate(props.post.created_at)

const commentForm = useForm({
    body: '',
})

const addComment = () => commentForm.post(route('posts.comments.store', props.post.id), {
    preserveScroll: true,
    onSuccess: () => commentForm.reset('body')
})

const commentTextAreaRef = ref(null);
const commentIdBeingEdited = ref(null)
const commentBeingEdited = computed(() => props.comments.data.find(comment => comment.id === commentIdBeingEdited.value))

const editComment = (commentId) => {
    commentIdBeingEdited.value = commentId
    commentForm.body = commentBeingEdited.value?.body
    commentTextAreaRef.value?.focus()
}

const cancelEditComment = () => {
    commentIdBeingEdited.value = null;
    commentForm.body = '';
}

const { confirmation } = useConfirm();

const updateComment = async () => {
    if (! await confirmation('Are you sure you want to update this comment?')){
        commentTextAreaRef.value?.focus()
        return;
    }

    commentForm.put(route('comments.update', {
        comment: commentIdBeingEdited.value,
        page: props.comments.meta_current_page
    }), {
        preserveScroll: true,
        body: commentForm.body,
        onSuccess: cancelEditComment,
    })
}

const deleteComment = async (commentId) => {
    if(! await confirmation('Are you sure you want to delete this comment?')) {
        return
    }

    router.delete(route('comments.destroy', { comment: commentId, page: props.comments.meta.current_page}), {
        preserveScroll: true
    })
}

</script>

<template>
    <AppLayout :title="post.title">
        <Container>
            <h1 class="text-2xl font-bold">{{ post.title }}</h1>
            <span class="block mt-1 text-sm text-gray-600">{{ formattedDate }} ago by {{ post.user.name }}</span>

            <article class="mt-6 prose prose-md max-w-none" v-html="post.html">
            </article>

            <div class="mt-12">
                <h2 class="text-xl font-semibold">Comments</h2>

                <form v-if="$page.props.auth.user" @submit.prevent="() => commentIdBeingEdited ? updateComment() : addComment()" class="mt-4">
                    <div>
                        <InputLabel for="body" class="sr-only">Comment</InputLabel>
                        <MarkdownEditor ref="commentTextAreaRef" id="body" v-model="commentForm.body" placeholder="Enter The Comment Text Here..." editorClass="min-h-[160px]"/>
                        <InputError :message="commentForm.errors.body" class="mt-1"/>
                    </div>

                    <PrimaryButton type="submit" class="mt-3" :disabled="commentForm.processing" v-text="commentIdBeingEdited ? 'Update Comment' : 'Create Comment'"></PrimaryButton>
                    <SecondaryButton v-if="commentIdBeingEdited" @click="cancelEditComment" class="ml-2">Cancel</SecondaryButton>
                </form>
                <ul class="divide-y mt-4">
                    <li v-for="comment in comments.data" :key="comment.id" class="px-2 py-4">
                        <Comment @edit="editComment" @delete="deleteComment" :comment="comment" />
                    </li>
                </ul>
                <Pagination :meta="comments.meta" :only="['comments']" />
            </div>
        </Container>
    </AppLayout>
</template>
