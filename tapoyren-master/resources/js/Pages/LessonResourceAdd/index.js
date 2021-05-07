import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import config from '../../config'

let course_id = null
let lesson_id = null

const _target = document.getElementById('react-lesson-resource-add');
if (_target) {
    const userApiToken = _target.dataset.token
    lesson_id = parseInt(_target.dataset.lessonId)
    course_id = parseInt(_target.dataset.courseId)
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

import { uploadDoc } from '../../helpers'

function LessonResourceAdd() {
    const resourceUploadRef = useRef()


    const [state, setState] = useState({
        resourceName: '',
        resourceFile: null,
        resources: [],
    });


    const saveResources = () => {
        const data = new FormData()

        data.append('resources', JSON.stringify(state.resources))

        axios.post(config.api + `/course/${course_id}/lesson/${lesson_id}/resource/add`, data).then(res => {
            Swal.fire('Əlavə edildi!', '', 'success')
            window.location.replace(config.host + `/admin/course/${course_id}/resource/add`)
        })
    }

    const handleResourceFile = e => {
        if (e.target.files.length === 1) {
            console.log('uploading doc', resourceUploadRef.current)
            uploadDoc(e.target.files[0], data => {
                setState({ ...state, resourceFile: data.path })
            })
        }
    }

    const handleResourceAdd = () => {
        const newResource = {
            title: state.resourceName,
            file: state.resourceFile
        }
        setState({
            ...state,
            resources: [...state.resources, newResource],
            resourceName: '',
            resourceFile: null
        })
        resourceUploadRef.current.value = ''
    }

    const removeResource = index => {
        const { resources } = state
        resources.splice(index, 1)
        setState({ ...state, resources })
    }



    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15, overflowY: 'scroll' }}>

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
                        accept=".pdf, .docx, .xlsx, .zip, .rar"
                        onChange={handleResourceFile}
                    />
                </div>

                <button
                    className="btn btn-danger btn-sm ml-8pt"
                    onClick={handleResourceAdd}>+</button>
            </div>

            <hr />


            <br />

            <button onClick={() => saveResources()} className="btn btn-primary btn-lg btn-block">Add Course</button>



        </div>
    );
}

if (_target) ReactDOM.render(<LessonResourceAdd />, _target);

