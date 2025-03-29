<script setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import Container from "@/Components/Container.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link } from "@inertiajs/vue3";
import {relativeDate} from "@/Utilities/date.js";

defineProps(['posts'])

const formattedDate = (post) => relativeDate(post.created_at)

</script>

<template>
    <AppLayout>
        <Container>
            <ul class="divide-y">
                <li v-for="post in posts.data" :key="post.id" class="flex justify-between items-baseline flex-col md:flex-row">
                    <Link :href="post.routes.show" class="block group px-2 py-4">
                        <span class="font-bold text-lg group-hover:text-indigo-500">{{ post.title }}</span>
                        <span class="block mt-1 text-sm text-gray-600">{{ formattedDate(post) }} ago by {{ post.user.name }}</span>
                    </Link>
                    <Link href="/" class="mb-2 rounded-full py-0.5 px-2 border border-pink-500 text-pink-500 hover:bg-indigo-500 hover:text-indigo-50">
                        {{ post.topic.name }}
                    </Link>
                </li>
            </ul>
            <Pagination :meta="posts.meta"/>
        </Container>
    </AppLayout>
</template>

<style scoped>

</style>
