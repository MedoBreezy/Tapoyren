import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import config from '../../config'

let course_id = null

const _target = document.getElementById('react-edit-course-data');
if (_target) {
    const userApiToken = _target.dataset.token
    course_id = _target.dataset.courseId
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

const uploadDoc = (doc, callback = null) => {
    const data = new FormData()
    data.append('file', doc)
    axios.post(config.api + '/upload/doc', data).then(res => {
        if (callback) callback(res.data)
    })
}

function EditCourseData() {
    const resourceUploadRef = useRef()

    const [data, setData] = useState({
        course: {},
    })

    const [state, setState] = useState({
        // EDIT
        updatingSectionIndex: null,
        removingSectionIndex: null,
        editingSectionIndex: null,

        updatingVideoIndex: null,
        editingVideoIndex: null,
        removingVideoIndex: null,

        // EDIT DATA
        edit_sectionTitle: '',
        edit_videoTitle: '',
        edit_videoPreview: false,
        edit_vimeoVideoId: '',


        resourceName: '',
        resourceFile: null,
        resources: [],

        sectionName: '',
        sections: [],

        videoName: '',
        videoPreview: false,
        vimeoVideoId: '',
    });

    useEffect(() => {

        axios.get(config.api + `/course/${course_id}/data`).then(res => {
            const { course } = res.data

            setData({ course })

            setState({
                ...state,
                resources: course.resources,
                sections: course.sections
            })
        })

    }, [])

    const handleResourceFile = e => {
        if (e.target.files.length === 1) {
            console.log('uploading doc', resourceUploadRef.current)
            setState({ ...state, resourceFile: e.target.files[0] })
        }
    }

    const handleResourceAdd = () => {
        let newResource = {
            title: state.resourceName,
        }
        uploadDoc(state.resourceFile, data => {
            const { path } = data
            newResource['file'] = path

            axios.post(config.api + `/course/${course_id}/resource/add`, {
                title: newResource.title,
                fileUrl: newResource.file
            }).then(res => {
                newResource['id'] = res.data.resourceId
                setState({
                    ...state,
                    resources: [...state.resources, newResource],
                    resourceName: '',
                    resourceFile: null
                })
                resourceUploadRef.current.value = ''
            }).catch(err => {
                alert('Error adding resource!')
                console.log(err.response)
            })


        })
    }

    const removeResource = index => {
        const { resources } = state
        const resource = resources[index]

        axios.post(config.api + `/course/${course_id}/resource/${resource.id}/remove`).then(res => {
            resources.splice(index, 1)
            setState({ ...state, resources })
        }).catch(err => {
            alert('Error removing resource!')
            console.log(err.response)
        })

    }

    const handleSectionAdd = e => {
        if (e.key === 'Enter') {
            let newSection = {
                title: state.sectionName,
                videos: []
            }

            axios.post(config.api + `/course/${course_id}/section/create`, {
                title: state.sectionName
            }).then(res => {
                newSection['id'] = res.data.sectionId
                setState({
                    ...state,
                    sectionName: '',
                    sections: [...state.sections, newSection]
                })
            })

        }
    }

    const removeSection = sectionIndex => {
        const { sections } = state
        const section = sections[sectionIndex]

        setState({
            ...state,
            removingSectionIndex: sectionIndex
        })

        axios.post(config.api + `/course/${course_id}/section/${section.id}/delete`).then(res => {
            sections.splice(sectionIndex, 1)
            setState({ ...state, sections, removingSectionIndex: null })
        }).catch(res => {
            setState({ ...state, removingSectionIndex: null })
        })

    }

    const removeVideo = (sectionIndex, videoIndex) => {
        const { sections } = state
        const section = sections[sectionIndex]
        const video = section['videos'][videoIndex]

        setState({
            ...state,
            removingVideoIndex: `${sectionIndex}_${videoIndex}`
        })

        axios.post(config.api + `/course/${course_id}/section/${section.id}/video/${video.id}/delete`).then(res => {
            sections[sectionIndex]['videos'].splice(videoIndex, 1)
            setState({ ...state, sections, removingVideoIndex: null })
        }).catch(res => {
            setState({ ...state, removingVideoIndex: null })
        })

    }

    const handleVideoAdd = sectionIndex => {
        const { sections } = state
        const section = sections[sectionIndex]
        const newVideo = {
            title: state.videoName,
            vimeoVideoId: state.vimeoVideoId,
            preview: state.videoPreview
        }

        setState({ ...state, addingVideoIndex: `${sectionIndex}` })

        axios.post(config.api + `/course/${course_id}/section/${section.id}/video/add`, newVideo).then(res => {
            section.videos.push({
                id: res.data.videoId,
                title: newVideo.title,
                vimeoVideoId: newVideo.vimeoVideoId,
                preview: newVideo.preview
            })

            sections[sectionIndex]['videos'] = section['videos']

            setState({
                ...state,
                videoName: '',
                videoPreview: false,
                vimeoVideoId: '',
                sections,
                addingVideoIndex: null
            })
        }).catch(err => {
            setState({ ...state, addingVideoIndex: null })
        })
    }

    const editSection = sectionIndex => {
        const section = state.sections[sectionIndex]
        setState({
            ...state,
            edit_sectionTitle: section.title,
            editingSectionIndex: sectionIndex
        })
    }

    const editVideo = (sectionIndex, videoIndex) => {
        const section = state.sections[sectionIndex]
        const video = section['videos'][videoIndex]
        setState({
            ...state,
            edit_videoTitle: video.title,
            edit_videoPreview: video.preview,
            edit_vimeoVideoId: video.vimeoVideoId,
            editingVideoIndex: `${sectionIndex}_${videoIndex}`
        })
    }

    const updateSection = () => {
        const section = state.sections[state.editingSectionIndex]
        const newTitle = state.edit_sectionTitle

        if (section.title !== newTitle) {
            setState({
                ...state,
                updatingSectionIndex: state.editingSectionIndex
            })

            axios.post(config.api + `/course/${course_id}/section/${section.id}/update`, {
                title: newTitle
            }).then(res => {
                const { sections } = state
                const updated_sections = sections
                updated_sections[state.editingSectionIndex].title = newTitle
                setState({ ...state, updatingSectionIndex: null, editingSectionIndex: null, edit_sectionTitle: '' })
            }).catch(res => {
                setState({ ...state, updatingSectionIndex: null })
            })
        }
        else setState({ ...state, editingSectionIndex: null, edit_sectionTitle: '' })

    }

    const updateVideo = (sectionIndex, videoIndex) => {
        const section = state.sections[sectionIndex]
        const video = section['videos'][videoIndex]

        const newTitle = state.edit_videoTitle
        const newVimeoVideoId = state.edit_vimeoVideoId
        const newPreview = state.edit_videoPreview

        if (video.title !== newTitle || video.preview !== newPreview || video.vimeoVideoId !== newVimeoVideoId) {
            setState({
                ...state,
                updatingVideoIndex: `${sectionIndex}_${videoIndex}`
            })

            axios.post(config.api + `/course/${course_id}/section/${section.id}/video/${video.id}/update`, {
                title: newTitle,
                vimeoVideoId: newVimeoVideoId,
                preview: newPreview
            }).then(res => {
                const { sections } = state
                sections[sectionIndex]['videos'][videoIndex].title = newTitle
                sections[sectionIndex]['videos'][videoIndex].vimeoVideoId = newVimeoVideoId
                sections[sectionIndex]['videos'][videoIndex].preview = newPreview
                setState({ ...state, updatingVideoIndex: null, editingVideoIndex: null, edit_videoTitle: '', edit_vimeoVideoId: '', edit_videoPreview: false })
            }).catch(res => {
                setState({ ...state, updatingVideoIndex: null })
            })
        }
        else setState({ ...state, editingVideoIndex: null, edit_videoTitle: '', edit_vimeoVideoId: '', edit_videoPreview: false })

    }


    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>

            <h2>{data.course.title}</h2>
            <hr />

            <div className="mb-8pt">
                <h4><b>Resources</b></h4>
            </div>
            {state.resources.map((resource, i) => {
                return <div key={i}>
                    {resource.title}
                    <button
                        className="btn btn-danger btn-sm ml-8pt"
                        onClick={() => removeResource(i)}>x</button>
                </div>
            })}

            <div className="d-flex align-items-center row">

                <div className="col-5 d-flex align-items-center mr-8pt">
                    <div className="mr-8pt">Title:</div>
                    <input
                        className="form-control"
                        type="text"
                        value={state.resourceName}
                        onChange={e => setState({ ...state, resourceName: e.target.value })}
                    />
                </div>

                <div className="col-5 d-flex align-items-center mr-8pt">
                    <div className="mr-8pt">File:</div>
                    <input
                        ref={resourceUploadRef}
                        className="form-control"
                        type="file"
                        onChange={handleResourceFile}
                    />
                </div>

                <button
                    className="btn btn-danger btn-sm ml-8pt"
                    onClick={handleResourceAdd}>+</button>
            </div>

            <hr />

            <div className="mb-8pt">
                <h4><b>Sections</b></h4>
            </div>
            {state.sections.map((section, sectionIndex) => {
                return <div key={sectionIndex} style={{ margin: '20px 0', border: '1px solid #d8d8d8', padding: 4, borderRadius: 2 }}>
                    <div style={{ opacity: state.updatingSectionIndex === sectionIndex ? 0.7 : 1 }}>
                        {state.updatingSectionIndex === sectionIndex && <h3>Updating...</h3>}
                        {state.removingSectionIndex === sectionIndex && <h3>Removing...</h3>}
                        {state.removingSectionIndex !== sectionIndex && <div style={{ display: state.updatingSectionIndex === sectionIndex ? 'none' : 'block' }}>
                            {state.editingSectionIndex === sectionIndex ? (
                                <>
                                    <input type="text"
                                        value={state.edit_sectionTitle}
                                        onChange={e => setState({ ...state, edit_sectionTitle: e.target.value })}
                                        style={{ border: 0, background: 'transparent' }} />
                                    <button onClick={() => updateSection()} className="btn btn-success btn-sm">✓</button>
                                </>
                            ) : (
                                    <a href="#" onClick={() => editSection(sectionIndex)}>{section.title}</a>
                                )}

                            {state.removingSectionIndex !== sectionIndex && <button
                                className="btn btn-danger btn-sm ml-8pt"
                                onClick={() => removeSection(sectionIndex)}>x</button>}
                        </div>}
                    </div>

                    <div style={{ background: '#fff', borderRadius: 4, padding: 10 }}>
                        <div style={{ padding: 15 }}>
                            {section.videos.map((video, videoIndex) => {
                                return <div key={videoIndex} className="mb-8pt d-flex align-items-center">
                                    {state.editingVideoIndex === `${sectionIndex}_${videoIndex}` ? (
                                        <>
                                            <input type="checkbox"
                                                className="form-control"
                                                style={{ width: 35, marginRight: 10 }}
                                                onChange={e => setState({ ...state, edit_videoPreview: e.target.checked })}
                                                checked={state.edit_videoPreview} />
                                            <input type="text"
                                                value={state.edit_videoTitle}
                                                onChange={e => setState({ ...state, edit_videoTitle: e.target.value })}
                                                style={{ border: '1px solid green', padding: 4, borderRadius: 2, background: 'transparent', marginRight: 10 }} />
                                            <input type="text"
                                                value={state.edit_vimeoVideoId}
                                                onChange={e => setState({ ...state, edit_vimeoVideoId: e.target.value })}
                                                style={{ border: '1px solid green', padding: 4, borderRadius: 2, background: 'transparent' }} />
                                            <button onClick={() => updateVideo(sectionIndex, videoIndex)} className="btn btn-success btn-sm ml-8pt">✓</button>
                                        </>
                                    ) : (
                                            <a href="#" onClick={() => editVideo(sectionIndex, videoIndex)}>{video.title}</a>
                                        )}

                                    <button
                                        className="btn btn-danger btn-sm ml-8pt"
                                        onClick={() => removeVideo(sectionIndex, videoIndex)}>x</button>
                                </div>
                            })}
                            <div className="d-flex align-items-center">
                                <div className="mr-8pt">Preview:</div>
                                <input
                                    className="form-control"
                                    type="checkbox"
                                    style={{ width: 80, marginRight: 20 }}
                                    checked={state.videoPreview}
                                    onChange={e => setState({ ...state, videoPreview: e.target.checked })}
                                />
                                <input
                                    placeholder="Title"
                                    className="form-control"
                                    style={{ marginRight: 20 }}
                                    type="text"
                                    value={state.videoName}
                                    onChange={e => setState({ ...state, videoName: e.target.value })}
                                />
                                <input
                                    placeholder="Vimeo Video ID"
                                    className="form-control"
                                    type="text"
                                    value={state.vimeoVideoId}
                                    onChange={e => setState({ ...state, vimeoVideoId: e.target.value })}
                                />
                                <button
                                    className="btn btn-danger btn-sm ml-8pt"
                                    onClick={() => handleVideoAdd(sectionIndex)}>+</button>
                            </div>
                        </div>
                    </div>
                </div>
            })}
            <input
                className="form-control"
                type="text"
                value={state.sectionName}
                onChange={e => setState({ ...state, sectionName: e.target.value })}
                onKeyDown={e => handleSectionAdd(e)}
            />




        </div>
    );
}

if (_target) ReactDOM.render(<EditCourseData />, _target);

