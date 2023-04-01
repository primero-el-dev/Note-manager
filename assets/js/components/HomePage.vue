<template>
    <div>
        <div class="mb-3">
            <h1 class="ms-auto d-inline-block">Notes</h1>

            <button role="button" class="btn btn-success float-right" data-bs-toggle="modal" data-bs-target="#noteModal" @click="() => { setEditedNotId(); setNoteForm(); }">
                Add note
            </button>
            <button role="button" class="btn btn-muted float-right" data-bs-toggle="modal" data-bs-target="#searchModal">
                Search notes
            </button>
        </div>

         <div class="container-fluid">
            <div class="row">
                <div v-for="note in selectedNotes" class="col-12 col-sm-6 col-md-4 col-lg-3 p-1">
                    <div class="card note" :data-note-id="note.id">
                        <div class="card-body">
                            <div class="card-title mb-3">
                                <h5>{{ note.title }}</h5>
                                <button role="button" class="btn btn-sm btn-danger float-right" @click="deleteNote">Delete</button>
                                <button role="button" class="btn btn-sm btn-primary float-right mr-2" @click="editNote" data-bs-toggle="modal" data-bs-target="#noteModal">Edit</button>
                                <small class="text-muted">{{ this.formatDateString(note.createdAt) }}</small>
                            </div>
                            <p class="card-text">{{ note.content }}</p>
                            <p v-for="ap in note.additionalParameters" class="card-text">
                                <strong>{{ ap.name }}:</strong> {{ ap.value }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="noteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form key="noteFormKey" ref="noteForm" :onSubmit="onNoteSave" class="mt-3">
                    <div class="modal-body pt-0">
                        <form-control 
                            ref="noteTitle" 
                            v-model="noteForm.title" 
                            label="Title" 
                            placeholder="Title" 
                            :error="this.noteForm.errors.title"
                        />
                        <form-control 
                            ref="noteContent" 
                            v-model="noteForm.content" 
                            type="textarea" 
                            label="Content" 
                            placeholder="Content" 
                            :error="this.noteForm.errors.content"
                        />
                        <div 
                            v-for="af in noteForm.additionalParameters" 
                            :key="af.id" 
                            class="additional-field" 
                            :data-field-id="af.id"
                        >
                            <button type="button" class="btn btn-sm btn-danger float-right" @click="deleteNoteParameter">
                                Delete parameter
                            </button>
                            <form-control 
                                label="Parameter name" 
                                :value="af.name" 
                                v-model="af.name" 
                                type="text" 
                                :name="'name_' + af.id" 
                                error="" 
                                placeholder="Parameter name"
                            />
                            <form-control
                                label="Parameter value" 
                                :value="af.value" 
                                v-model="af.value" 
                                type="text" 
                                :name="'value_' + af.id" 
                                error="" 
                                placeholder="Parameter value"
                            />
                        </div>
                        <button class="btn btn-primary mt-2 w-100" type="button" @click="addNoteParameter">Add parameter</button>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success w-100" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="searchModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form key="noteFormKey" ref="searchForm" :onSubmit="onNoteSearch" class="mt-3">
                    <div class="modal-body pt-0">
                        <form-control 
                            ref="noteSearch" 
                            v-model="noteSearch.value" 
                            label="Search value" 
                            placeholder="Search value" 
                        />
                        
                        <div class="form-check my-2">
                            <input class="form-check-input" type="checkbox" v-model="this.noteSearch.includeAdditionalParameters" id="includeAdditionalParametersCheckbox" checked>
                            <label class="form-check-label" for="includeAdditionalParametersCheckbox">
                                Include additional parameters
                            </label>
                        </div>
                        
                        <div class="form-check my-2">
                            <input class="form-check-input" type="checkbox" v-model="this.noteSearch.includeDateCreated" id="includeDateCreatedCheckbox" checked>
                            <label class="form-check-label" for="includeDateCreatedCheckbox">
                                Include creation date
                            </label>
                        </div>
                        
                        <label class="my-2 w-100">
                            Sort by
                            <select v-model="this.noteSearch.sortBy" class="form-select ">
                                <option value="none">-</option>
                                <option value="title">Title</option>
                                <option value="title">Content</option>
                                <option value="createdAt">Creation date</option>
                                <option 
                                    v-for="parameterName in this.getAllAdditionalParameterNames()" 
                                    :value="this.noteSearch.parameterInputPrefix + parameterName"
                                >
                                    {{ this.noteSearch.parameterInputPrefix + parameterName }}
                                </option>
                            </select>
                        </label>

                        <div class="my-2">
                            Sort with missing parameters as
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortNullAs" id="sortNullAsFirst" v-model="this.noteSearch.sortNullAs" value="first" checked>
                                <label class="form-check-label" for="sortNullAsFirst">
                                    First
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortNullAs" id="sortNullAsLast" v-model="this.noteSearch.sortNullAs" value="last">
                                <label class="form-check-label" for="sortNullAsLast">
                                    Last
                                </label>
                            </div>
                        </div>
                        
                        <div class="my-2">
                            Sort direction
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortByDirection" id="sortByDirectionAsc" v-model="this.noteSearch.sortByDirection" value="asc" checked>
                                <label class="form-check-label" for="sortByDirectionAsc">
                                    Ascending
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sortByDirection" id="sortByDirectionDesc" v-model="this.noteSearch.sortByDirection" value="desc">
                                <label class="form-check-label" for="sortByDirectionDesc">
                                    Descending
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success w-100" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import { backendRoutes, prepareBackendRoute } from '../router'
    import FormControl from './FormControl'
    import store, { updateSessionFromResponse } from '../store'
    import { pick } from '../functions'
    import * as $ from 'jquery'

    export default {
        components: {
            FormControl,
        },
        data() {
            try {
                axios.get(backendRoutes.getNotes.path, {
                    method: backendRoutes.getNotes.method,
                    withCredentials: true,
                })
                    .then(response => {
                        updateSessionFromResponse(response)
                        this.notes = response.data.data.notes
                        this.selectedNotes = this.notes
                    })
            }
            catch (error) {
                updateSessionFromResponse(error.response)
            }

            return {
                notes: [],
                editedNoteId: null,
                noteSearch: {
                    value: '',
                    includeAdditionalParameters: false,
                    includeDateCreated: false,
                    sortBy: 'none',
                    sortNullAs: 'first',
                    sortByDirection: 'asc',
                    parameterInputPrefix: 'parameter - ',
                },
                noteForm: {
                    title: '',
                    content: '',
                    additionalParameters: [],
                    additionalParametersMaxId: 0,
                    errors: {},
                },
            }
        },
        computed: {
            selectedNotes: {
                get() {
                    let selected = this.notes
                    if (this.noteSearch.value !== '') {
                        selected = this.notes.filter(n => {
                            if (n.title.includes(this.noteSearch.value) || n.content.includes(this.noteSearch.value)) {
                                return true
                            }
                            if (this.noteSearch.includeAdditionalParameters) {
                                for (let ap of n.additionalParameters) {
                                    if (ap.name.includes(this.noteSearch.value) || ap.value.includes(this.noteSearch.value)) {
                                        return true
                                    }
                                }
                            }
                            if (this.noteSearch.includeDateCreated && n.createdAt.includes(this.noteSearch.value)) {
                                return true
                            }
                            return false
                        })
                    }

                    if (this.noteSearch.sortBy !== 'none') {
                        let sortByField = this.noteSearch.sortBy
                        if (['title', 'content', 'createdAt'].includes(sortByField)) {
                            selected.sort((a, b) => {
                                let first = a[sortByField]
                                let second = a[sortByField]

                                if (first === second) {
                                    return 0
                                }
                                else if ((this.noteSearch.sortByDirection === 'asc') ? (first < second) : (first > second)) {
                                    return -1
                                }
                                else {
                                    return 1
                                }
                            })
                        }
                        else {
                            sortByField = sortByField.substring(this.noteSearch.parameterInputPrefix.length)

                            selected.sort((a, b) => {
                                let first = a.additionalParameters.filter(ap => ap.name === sortByField)
                                first = (first[0] !== undefined && first[0] !== null) ? first[0].value : null
                                let second = b.additionalParameters.filter(ap => ap.name === sortByField)
                                second = (second[0] !== undefined && second[0] !== null) ? second[0].value : null

                                if (first === second) {
                                    return 0
                                }
                                else if (first === null && second !== null) {
                                    if (this.noteSearch.sortNullAs === 'first') {
                                        return -1
                                    }
                                    else {
                                        return 1
                                    }
                                }
                                else if (first !== null && second === null) {
                                    if (this.noteSearch.sortNullAs === 'first') {
                                        return 1
                                    }
                                    else {
                                        return -1
                                    }
                                }
                                else if ((this.noteSearch.sortByDirection === 'asc') ? (first < second) : (first > second)) {
                                    return -1
                                }
                                else {
                                    return 1
                                }
                            })
                        }
                    }

                    return selected
                },
            }
        },
        methods: {
            getAllAdditionalParameterNames() {
                let params = []
                for (let note of this.notes) {
                    for (let ap of note.additionalParameters) {
                        if (!params.includes(ap.name) && ap.name.trim() !== '') {
                            params.push(ap.name)
                        }
                    }
                }

                return params
            },
            onNoteSearch(e) {
                e.preventDefault()
                this.hideModal('#searchModal')
            },
            formatDateString(date) {
                let d = new Date(date)
                let addTrailingZero = (value) => (value < 10) ? ('0' + value) : value
                return addTrailingZero(d.getHours()) +
                    ':' + addTrailingZero(d.getMinutes()) +
                    ' ' + addTrailingZero(d.getDate()) +
                    '.' + addTrailingZero(d.getMonth()+1) +
                    '.' + d.getFullYear()
            },
            setEditedNotId(id) {
                this.editedNoteId = (id !== undefined) ? id : null
            },
            deleteNoteParameter(e) {
                let element = e.target.closest('.additional-field')
                this.noteForm.additionalParameters = this.noteForm.additionalParameters.filter(af => af.id != element.dataset.fieldId)
            },
            addNoteParameter(e) {
                this.noteForm.additionalParameters.push({ id: ++this.noteForm.additionalParametersMaxId, name: '', value: '' })
            },
            async onNoteSave(e) {
                e.preventDefault()

                try {
                    let response
                    let formData = pick(this.noteForm, ['title', 'content', 'additionalParameters'])
                    formData.additionalParameters = formData.additionalParameters.map(ap => pick(ap, ['name', 'value']))

                    if (this.editedNoteId === null) {
                        let method = backendRoutes.createNote.method.toLowerCase()
                        response = await axios[method](backendRoutes.createNote.path, formData)
                        this.notes.push(response.data.data.note)
                    }
                    else {
                        let method = backendRoutes.updateNote.method.toLowerCase()
                        response = await axios[method](prepareBackendRoute('updateNote', { id: this.editedNoteId }), formData)
                        let newVersion = response.data.data.note
                        for (let i in this.notes) {
                            if (this.notes[i].id == newVersion.id) {
                                this.notes[i] = newVersion
                            }
                        }
                    }

                    updateSessionFromResponse(response)
                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                    this.setNoteForm()
                    this.hideModal('#noteModal')
                }
                catch (error) {
                    updateSessionFromResponse(error.response)
                    console.log(error)
                }
            },
            editNote(e) {
                this.editedNoteId = e.target.closest('.note').dataset.noteId
                let editedNote = null
                for (let note of this.notes) {
                    if (note.id === this.editedNoteId) {
                        editedNote = note
                        break
                    }
                }

                this.setNoteForm(editedNote)
            },
            async deleteNote(e) {
                if (!confirm('Are you sure you want to delete this note?')) {
                    return
                }
                let noteId = e.target.closest('.note').dataset.noteId

                try {
                    let response
                    let method = backendRoutes.deleteNote.method.toLowerCase()
                    response = await axios[method](prepareBackendRoute('deleteNote', { id: noteId }), null)

                    for (let i in this.notes) {
                        if (this.notes[i].id == noteId) {
                            delete this.notes[i]
                        }
                    }
                    this.notes = this.notes.filter(n => n)
                    console.log(response)
                
                    updateSessionFromResponse(response)
                    store.commit('addFlash', { message: response.data.data.message, type: 'success' })
                }
                catch (error) {
                    updateSessionFromResponse(error.response)
                    console.log(error)
                }
            },
            setNoteForm(note = null) {
                if (note) {
                    this.noteForm.title = note?.title
                    this.noteForm.content = note?.content
                    this.noteForm.additionalParameters = note?.additionalParameters
                }
                else {
                    this.noteForm.title = ''
                    this.noteForm.content = ''
                    this.noteForm.additionalParameters = []
                }

                this.$refs.noteTitle.value = this.noteForm.title
                this.$refs.noteContent.value = this.noteForm.content
            },
            hideModal(id) {
                $(id).removeClass('show')
                $('.modal-backdrop').remove()
                $(id).hide()
                $('body').removeClass('modal-open')
                $('body').attr('style', '')
            },
        },
    }
</script>