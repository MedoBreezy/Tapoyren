import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

import { uploadImage } from '../../helpers'

const _target = document.getElementById('react-add-course');
if (_target) {
    const userApiToken = _target.dataset.token
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

function AddCourse() {
    const [data, setData] = useState({
        courses: [],
        categories: [],
        instructors: [],
        getData: false,
    })

    const [state, setState] = useState({
        title: '',
        description: '',
        language: '',
        difficulty: '',
        has_trial: false,
        about: '',

        startCourseMail: '',
        finishCourseMail: '',

        category_id: '',

        priceType: '',

        monthlyPrice: 0.00,
        quarterlyPrice: 0.00,
        semiannuallyPrice: 0.00,
        annuallyPrice: 0.00,

        parentCourseId: '',
        instructorId: '',
        thumbnail: '',

        whatYouWillLearn: '',
        whatYouWillLearnList: [],

    });

    useEffect(() => {
        if (!data.getData) {
            if (data.courses.length === 0) axios.get(config.api + '/course/list').then(res => {
                if (res.data.courses.length > 0) setData({ ...data, courses: [...res.data.courses] })
            })

            if (data.categories.length === 0) axios.get(config.api + '/category/list').then(res => {
                if (res.data.categories.length > 0) setData({ ...data, categories: [...res.data.categories] })
            })

            if (data.instructors.length === 0) axios.get(config.api + '/instructor/list').then(res => {
                if (res.data.instructors.length > 0) setData({ ...data, instructors: [...res.data.instructors] })
            })

            setData({ ...data, getData: true })
        }
    }, [data.getData])

    const saveCourse = () => {
        const courseData = new FormData()

        courseData.append('title', state.title)
        courseData.append('description', state.description)
        courseData.append('language', state.language)
        courseData.append('difficulty', state.difficulty)
        courseData.append('startCourseMail', state.finishCourseMail)
        courseData.append('finishCourseMail', state.startCourseMail)
        courseData.append('category_id', state.category_id)
        if (state.priceType === 'paid') courseData.append('has_trial', state.has_trial ? 1 : 0)
        courseData.append('about', state.about)
        courseData.append('priceType', state.priceType)
        courseData.append('thumbnail', state.thumbnail)
        courseData.append('whatYouWillLearnList', JSON.stringify(state.whatYouWillLearnList))
        courseData.append('instructor_id', state.instructorId)

        if (state.priceType === 'paid') {
            courseData.append('monthlyPrice', state.monthlyPrice)
            courseData.append('quarterlyPrice', state.quarterlyPrice)
            courseData.append('semiannuallyPrice', state.semiannuallyPrice)
            courseData.append('annuallyPrice', state.annuallyPrice)
        }
        axios.post(config.api + '/course/add', courseData).then(res => {
            const courseId = res.data.id
            alert('Kurs əlavə edildi!')
            window.location.replace(config.host + `/admin/course/${courseId}/edit_data`)
        })
    }

    const handleAboutChange = (content, editor) => {
        setState({ ...state, about: content })
    }

    const handleWhatYouWillLearn = e => {
        if (e.key === 'Enter') {

            const { value } = e.target

            const newItem = {
                title: value
            }

            setState({
                ...state,
                whatYouWillLearn: '',
                whatYouWillLearnList: [...state.whatYouWillLearnList, newItem]
            })

        }
    }

    const removeWhatYouWillLearn = index => {
        const { whatYouWillLearnList } = state
        whatYouWillLearnList.splice(index, 1)
        setState({ ...state, whatYouWillLearnList })
    }

    const handleThumbnail = e => {
        if (e.target.files.length === 1) {
            console.log('uploading thumbnail')
            uploadImage(e.target.files[0], data => {
                setState({ ...state, thumbnail: data.path })
            })
        }
    }




    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>

            <input
                className="form-control"
                type="text" placeholder="Title"
                value={state.title}
                onChange={e => setState({ ...state, title: e.target.value })}
            />
            <br />

            <select
                className="form-control"
                onChange={e => setState({ ...state, category_id: e.target.value })}>
                <option value="">Select Category</option>
                {data.categories.map((category, categoryIndex) => {
                    return <optgroup key={categoryIndex} label={category.title}>
                        {category.sub_categories.map((sub_category, subCategoryIndex) => {
                            return <option
                                key={subCategoryIndex}
                                value={sub_category.id}>{sub_category.title}</option>
                        })}
                    </optgroup>
                })}
            </select>
            <br />

            <input
                className="form-control"
                type="text" placeholder="Description"
                value={state.description}
                onChange={e => setState({ ...state, description: e.target.value })}
            />
            <br />

            <input
                className="form-control"
                type="text" placeholder="Language"
                value={state.language}
                onChange={e => setState({ ...state, language: e.target.value })}
            />
            <br />

            <select className="form-control" onChange={e => setState({ ...state, difficulty: e.target.value })}>
                <option value="">Select Course Difficulty</option>
                <option value="0">Beginner</option>
                <option value="1">Intermediate</option>
                <option value="2">Advanced</option>
            </select>
            <br />

            <select
                className="form-control"
                onChange={e => setState({ ...state, instructorId: e.target.value })}>
                <option value="">Select Instructor</option>
                {data.instructors.map(instructor => {
                    return <option key={instructor.id} value={instructor.id}>{instructor.name}</option>
                })}
            </select>

            <br />

            <br />
            <h5>Start Course Message</h5>
            <Editor height={250} value={state.startCourseMail} handleChange={(content, editor) => setState({ ...state, startCourseMail: content })} />
            <br />

            <br />
            <h5>Finish Course Message</h5>
            <Editor height={250} value={state.finishCourseMail} handleChange={(content, editor) => setState({ ...state, finishCourseMail: content })} />
            <br />

            <br />
            <h5>About Course</h5>
            <Editor height={250} value={state.about} handleChange={handleAboutChange} />
            <br />

            <div className="d-flex align-items-center">
                <div className="mr-8pt">Thumbnail:</div>
                <input
                    className="form-control"
                    type="file"
                    onChange={handleThumbnail}
                />
            </div>
            <br />

            <select
                className="form-control"
                onChange={e => setState({ ...state, priceType: e.target.value, has_trial: false })}>
                <option value="">Select Course Price</option>
                <option value="free">Free</option>
                <option value="paid">Paid</option>
            </select>
            <br />

            {state.priceType === 'paid' && (
                <div>

                    Monthly Price (AZN):
                    <input
                        className="form-control"
                        type="text"
                        value={state.monthlyPrice}
                        onChange={e => setState({ ...state, monthlyPrice: e.target.value })}
                    />
                    <br />

               Quarterly Price (AZN):
                    <input
                        className="form-control"
                        type="text"
                        value={state.quarterlyPrice}
                        onChange={e => setState({ ...state, quarterlyPrice: e.target.value })}
                    />
                    <br />

               Semi-Annually Price (AZN):
                    <input
                        className="form-control"
                        type="text"
                        value={state.semiannuallyPrice}
                        onChange={e => setState({ ...state, semiannuallyPrice: e.target.value })}
                    />
                    <br />

               Annually Price (AZN):
                    <input
                        className="form-control"
                        type="text"
                        value={state.annuallyPrice}
                        onChange={e => setState({ ...state, annuallyPrice: e.target.value })}
                    />
                    <br />

                    {state.priceType === 'paid' && (
                        <div style={{ display: 'flex', alignItems: 'center' }}>
                            <input
                                className="form-control"
                                style={{ width: 50 }}
                                type="checkbox"
                                checked={state.has_trial}
                                onChange={e => setState({ ...state, has_trial: e.target.checked })}
                            />
                            <span>Free Trial</span>
                        </div>
                    )}

                </div>
            )}

            <hr />

         What You will Learn List:<br />
            {state.whatYouWillLearnList.map((item, i) => {
                return <div key={i}>
                    {item.title}
                    <button
                        className="btn btn-danger btn-sm ml-8pt"
                        onClick={() => removeWhatYouWillLearn(i)}>x</button>
                </div>
            })}
            <input
                className="form-control"
                type="text"
                value={state.whatYouWillLearn}
                onChange={e => setState({ ...state, whatYouWillLearn: e.target.value })}
                onKeyDown={e => handleWhatYouWillLearn(e)}
            />
            <hr />



            <button onClick={() => saveCourse()} className="btn btn-primary btn-lg btn-block">Add Course</button>



        </div>
    );
}

if (_target) ReactDOM.render(<AddCourse />, _target);

