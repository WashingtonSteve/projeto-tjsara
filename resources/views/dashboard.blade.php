@extends('layouts.app')

@section('title', 'Dashboard · Task Management API')

@section('content')
<div x-data="taskDashboard()" x-init="init()" class="min-h-full bg-slate-50">

    <!-- Top bar -->
    <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <span class="font-semibold text-slate-900">Task Management</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="/docs/api" class="hidden text-sm text-slate-500 hover:text-slate-700 sm:inline">API Docs</a>
                <span class="text-sm text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">

        <!-- Error banner -->
        <div x-show="error" x-cloak x-transition class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <span x-text="error"></span>
        </div>

        <!-- Stats -->
        <div class="mb-8 grid grid-cols-3 gap-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Total</p>
                <p class="mt-1 text-2xl font-semibold text-slate-900" x-text="stats.total"></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Pending</p>
                <p class="mt-1 text-2xl font-semibold text-amber-600" x-text="stats.pending"></p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Completed</p>
                <p class="mt-1 text-2xl font-semibold text-emerald-600" x-text="stats.completed"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">

                <!-- Quick add -->
                <form @submit.prevent="createTask()" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input
                            type="text"
                            x-model="newTask.title"
                            placeholder="What needs to be done?"
                            required
                            class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        <input
                            type="date"
                            x-model="newTask.due_date"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                        <button
                            type="submit"
                            :disabled="creating"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <span x-show="!creating">Add task</span>
                            <span x-show="creating" x-cloak>Adding…</span>
                        </button>
                    </div>
                </form>

                <!-- Filters -->
                <div class="mb-4 flex gap-2">
                    <template x-for="option in ['all', 'pending', 'completed']" :key="option">
                        <button
                            @click="filter = option"
                            :class="filter === option ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200'"
                            class="rounded-full px-3 py-1 text-xs font-medium capitalize shadow-sm"
                            x-text="option"
                        ></button>
                    </template>
                </div>

                <!-- Loading -->
                <div x-show="loading" class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-sm text-slate-400">
                    Loading tasks…
                </div>

                <!-- Empty state -->
                <div x-show="!loading && filteredTasks.length === 0" x-cloak class="rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-sm text-slate-400">
                    No tasks here yet.
                </div>

                <!-- Task list -->
                <ul class="space-y-3" x-show="!loading">
                    <template x-for="task in filteredTasks" :key="task.id">
                        <li class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex items-start gap-3 p-4">
                                <button
                                    @click="task.status === 'pending' && completeTask(task)"
                                    :disabled="task.status === 'completed'"
                                    :class="task.status === 'completed' ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 hover:border-indigo-500'"
                                    class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2"
                                    title="Mark complete"
                                >
                                    <svg x-show="task.status === 'completed'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6L9 17l-5-5"></path>
                                    </svg>
                                </button>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p
                                            :class="task.status === 'completed' ? 'line-through text-slate-400' : 'text-slate-900'"
                                            class="font-medium"
                                            x-text="task.title"
                                        ></p>
                                        <span
                                            x-show="task.due_date"
                                            x-text="task.due_date"
                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500"
                                        ></span>
                                        <template x-for="tag in task.tags" :key="tag.id">
                                            <span class="flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-xs text-indigo-700">
                                                <span x-text="tag.name"></span>
                                                <button @click="detachTag(task, tag)" class="text-indigo-400 hover:text-indigo-700">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                    <p x-show="task.description" x-text="task.description" class="mt-1 text-sm text-slate-500"></p>
                                    <p
                                        x-show="task.subtasks.length > 0"
                                        class="mt-1 text-xs text-slate-400"
                                        x-text="task.subtasks.filter(s => s.completed).length + ' / ' + task.subtasks.length + ' subtasks'"
                                    ></p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <button @click="toggleExpand(task)" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="expanded === task.id ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 9l6 6 6-6"></path>
                                        </svg>
                                    </button>
                                    <button @click="deleteTask(task)" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Expanded panel -->
                            <div x-show="expanded === task.id" x-cloak x-transition class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                                <!-- Subtasks -->
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Subtasks</p>
                                <ul class="mb-2 space-y-1">
                                    <template x-for="subtask in task.subtasks" :key="subtask.id">
                                        <li class="flex items-center gap-2 text-sm">
                                            <button
                                                @click="!subtask.completed && completeSubtask(task, subtask)"
                                                :class="subtask.completed ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300'"
                                                class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2"
                                            >
                                                <svg x-show="subtask.completed" xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 6L9 17l-5-5"></path>
                                                </svg>
                                            </button>
                                            <span :class="subtask.completed ? 'line-through text-slate-400' : 'text-slate-700'" x-text="subtask.title"></span>
                                            <button @click="removeSubtask(task, subtask)" class="ml-auto text-slate-300 hover:text-red-500">&times;</button>
                                        </li>
                                    </template>
                                </ul>
                                <form @submit.prevent="addSubtask(task)" class="mb-4 flex gap-2">
                                    <input
                                        type="text"
                                        x-model="subtaskDrafts[task.id]"
                                        placeholder="Add a subtask…"
                                        class="flex-1 rounded-lg border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                    <button type="submit" class="rounded-lg bg-white border border-slate-300 px-3 py-1 text-sm text-slate-600 shadow-sm hover:bg-slate-50">Add</button>
                                </form>

                                <!-- Tags -->
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Tags</p>
                                <form @submit.prevent="attachTag(task)" class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="tagDrafts[task.id]"
                                        placeholder="Add a tag…"
                                        class="flex-1 rounded-lg border border-slate-300 px-2 py-1 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                    <button type="submit" class="rounded-lg bg-white border border-slate-300 px-3 py-1 text-sm text-slate-600 shadow-sm hover:bg-slate-50">Add</button>
                                </form>
                            </div>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Activity feed -->
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Recent activity</p>
                    <div x-show="activity.length === 0" class="text-sm text-slate-400">Nothing yet.</div>
                    <ul class="space-y-3">
                        <template x-for="entry in activity" :key="entry.id">
                            <li class="text-sm text-slate-600">
                                <p x-text="entry.description"></p>
                                <p class="text-xs text-slate-400" x-text="formatTime(entry.created_at)"></p>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function taskDashboard() {
    return {
        tasks: [],
        activity: [],
        loading: true,
        creating: false,
        error: null,
        filter: 'all',
        expanded: null,
        newTask: { title: '', due_date: '' },
        subtaskDrafts: {},
        tagDrafts: {},

        async init() {
            await Promise.all([this.loadTasks(), this.loadActivity()]);
            this.loading = false;
        },

        get filteredTasks() {
            if (this.filter === 'pending') return this.tasks.filter(t => t.status === 'pending');
            if (this.filter === 'completed') return this.tasks.filter(t => t.status === 'completed');
            return this.tasks;
        },

        get stats() {
            return {
                total: this.tasks.length,
                pending: this.tasks.filter(t => t.status === 'pending').length,
                completed: this.tasks.filter(t => t.status === 'completed').length,
            };
        },

        formatTime(iso) {
            return new Date(iso).toLocaleString();
        },

        toggleExpand(task) {
            this.expanded = this.expanded === task.id ? null : task.id;
        },

        replaceTask(updated) {
            const index = this.tasks.findIndex(t => t.id === updated.id);
            if (index !== -1) this.tasks.splice(index, 1, updated);
        },

        async request(method, url, body = null) {
            const headers = {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            };
            if (body) headers['Content-Type'] = 'application/json';

            const response = await fetch(url, {
                method,
                headers,
                credentials: 'same-origin',
                body: body ? JSON.stringify(body) : undefined,
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.message || `Request failed (${response.status})`);
            }

            if (response.status === 204) return null;
            return response.json();
        },

        async loadTasks() {
            try {
                const { data } = await this.request('GET', '/api/tasks');
                this.tasks = data;
            } catch (e) {
                this.error = e.message;
            }
        },

        async loadActivity() {
            try {
                const { data } = await this.request('GET', '/api/activity');
                this.activity = data;
            } catch (e) {
                this.error = e.message;
            }
        },

        async createTask() {
            if (!this.newTask.title.trim()) return;
            this.creating = true;
            this.error = null;
            try {
                const payload = { title: this.newTask.title };
                if (this.newTask.due_date) payload.due_date = this.newTask.due_date;
                const { data } = await this.request('POST', '/api/tasks', payload);
                this.tasks.unshift(data);
                this.newTask = { title: '', due_date: '' };
            } catch (e) {
                this.error = e.message;
            } finally {
                this.creating = false;
            }
        },

        async completeTask(task) {
            this.error = null;
            try {
                const { data } = await this.request('POST', `/api/tasks/${task.id}/complete`);
                this.replaceTask(data);
                await this.loadActivity();
            } catch (e) {
                this.error = e.message;
            }
        },

        async deleteTask(task) {
            if (!confirm(`Delete "${task.title}"?`)) return;
            this.error = null;
            try {
                await this.request('DELETE', `/api/tasks/${task.id}`);
                this.tasks = this.tasks.filter(t => t.id !== task.id);
            } catch (e) {
                this.error = e.message;
            }
        },

        async addSubtask(task) {
            const title = (this.subtaskDrafts[task.id] || '').trim();
            if (!title) return;
            this.error = null;
            try {
                const { data } = await this.request('POST', `/api/tasks/${task.id}/subtasks`, { title });
                this.replaceTask(data);
                this.subtaskDrafts[task.id] = '';
            } catch (e) {
                this.error = e.message;
            }
        },

        async completeSubtask(task, subtask) {
            this.error = null;
            try {
                const data = await this.request('POST', `/api/tasks/${task.id}/subtasks/${subtask.id}/complete`);
                this.replaceTask(data);
            } catch (e) {
                this.error = e.message;
            }
        },

        async removeSubtask(task, subtask) {
            this.error = null;
            try {
                const data = await this.request('DELETE', `/api/tasks/${task.id}/subtasks/${subtask.id}`);
                this.replaceTask(data);
            } catch (e) {
                this.error = e.message;
            }
        },

        async attachTag(task) {
            const name = (this.tagDrafts[task.id] || '').trim();
            if (!name) return;
            this.error = null;
            try {
                const data = await this.request('POST', `/api/tasks/${task.id}/tags`, { name });
                this.replaceTask(data);
                this.tagDrafts[task.id] = '';
            } catch (e) {
                this.error = e.message;
            }
        },

        async detachTag(task, tag) {
            this.error = null;
            try {
                const data = await this.request('DELETE', `/api/tasks/${task.id}/tags/${tag.id}`);
                this.replaceTask(data);
            } catch (e) {
                this.error = e.message;
            }
        },
    };
}
</script>
@endpush
