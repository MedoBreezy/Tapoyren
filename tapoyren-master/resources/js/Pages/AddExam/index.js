import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import Editor from '../../components/Editor'

import config from '../../config'

import { uploadImage } from '../../helpers'

let course_id = null
let exam_id = null

const _target = document.getElementById('react-add-exam');
if (_target) {
    const userApiToken = _target.dataset.token
    course_id = (_target.dataset.courseId !== undefined) ? parseInt(_target.dataset.courseId) : null
    exam_id = (_target.dataset.examId !== undefined) ? parseInt(_target.dataset.examId) : null
    axios.defaults.headers.common['Authorization'] = "Bearer " + userApiToken;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

function AddExam() {

    const [data, setData] = useState({
        courses: [],
        courseSections: [],
        getData: false,
    })

    const [state, setState] = useState({
        editingQuestionIndex: null,
        addingAnswerQuestionIndex: null,
        editingAnswerId: null,
        editingAnswerTitle: '',
        savingExam: false,

        exam_id: exam_id,
        course_id: course_id,

        orderLectureId: null,


        examTitle: '',
        examDescription: '',
        examMinimumPoint: '',
        examType: '',
        examTime: '',


        time: '',

        questions: [],

        topic: '',
        questionTitle: '',
        questionExplanation: '',
        questionVVI: '',
        explanationVVI: '',
        questionAnswerType: '',
        questionAssignmentAnswer: '',
        relatedLectureId: '',

        answerTitle: '',
        answerCorrect: false,


    });

    useEffect(() => {
        if (!data.getData) {

            if (data.courses.length === 0) axios.get(config.api + '/course/list').then(res => {
                if (res.data.courses.length > 0) setData({ ...data, courses: [...res.data.courses] })
            }).catch(err => {
                alert('Error getting course list!')
            })

            if (state.course_id !== null && data.courseSections.length === 0) axios.get(config.api + `/course/${state.course_id}/sections/list`).then(res => {
                const { sections } = res.data
                setData({ ...data, courseSections: sections })
            })

            if (state.course_id !== null && state.exam_id !== null) axios.get(config.api + `/course/${state.course_id}/exam/${state.exam_id}/data`).then(res => {
                const { data } = res
                const questions = data.questions.map(question => {
                    return {
                        id: question.id,
                        title: question.title,
                        topic: question.topic,
			explanation: question.explanation,
                        relatedLectureId: question.relatedLectureId,
                        answerType: question.answer_type,
                        questionVVI: question.question_vimeoVideoId,
                        explanationVVI: question.explanation_vimeoVideoId,
                        status: question.status,
                        answers: question.answers,
                        singleChoiceAnswerId: question.singleChoiceAnswerId,
                        multipleChoiceAnswerIds: JSON.parse(question.multipleChoiceAnswerIds) || [],
                        assignmentAnswer: question.assignmentAnswer,
                    }
                })
                setState({
                    ...state,
                    examTitle: data.title,
                    examDescription: data.description,
                    examType: data.type,
                    examMinimumPoint: data.minimum_point,
                    examTime: data.time,
                    questions: questions,
                    orderLectureId: data.order_lecture_id
                })
            }).catch(err => {
                console.log(err.response)
            })

            setData({ ...data, getData: true })
        }
    }, [data.getData])

    const courseChange = id => {
        setState({ ...state, course_id: id })
        axios.get(config.api + `/course/${id}/sections/list`).then(res => {
            const { sections } = res.data
            setData({ ...data, courseSections: sections })
        })
    }

    const addQuestion = () => {
        let question = {
            title: state.questionTitle,
            topic: state.topic,
            explanation: state.questionExplanation,
            questionVVI: state.questionVVI,
            explanationVVI: state.explanationVVI,
            answerType: state.questionAnswerType,
            relatedLectureId: state.relatedLectureId,
            singleChoiceAnswerId: null,
            multipleChoiceAnswerIds: [],
            answers: [],
        }

        setState({ ...state, addingQuestion: true })
        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/add`, question).then(res => {
            question['id'] = res.data.questionId
            setState({
                ...state,
                questions: [...state.questions, question],
                questionTitle: '',
                topic: '',
                questionExplanation: '',
                questionHasVideo: false,
                questionVVI: '',
                explanationVVI: '',
                questionAnswerType: '',
                relatedLectureId: ''
            })
        }).catch(err => {
            Swal.fire('Error adding question!', '', 'error')
            setState({ ...state, addingQuestion: false })
        })


    }

    const updateQuestion = (questionIndex, key, value) => {
        const { questions } = state
        const question = questions[questionIndex]
        question[key] = value

        if (key === 'answerType' && (question['answers'].length > 0 || question.assignmentAnswer !== '')) {
            axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/answers/delete`).then(res => {
                question['singleChoiceAnswerId'] = null
                question['multipleChoiceAnswerIds'] = []
                question['assignmentAnswer'] = ''
                question['answers'] = []
                setState({ ...state, questions })
            }).catch(err => {
                alert('Error changing question answer type!')
            })
        }
        else setState({ ...state, questions })
    }

    const saveQuestion = () => {
        const { questions } = state
        const question = questions[state.editingQuestionIndex]

        setState({ ...state, updatingQuestionIndex: state.editingQuestionIndex })

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/update`, question).then(res => {
            setState({ ...state, updatingQuestionIndex: null, editingQuestionIndex: null })
            Swal.fire('Question updated!', '', 'success')
        }).catch(err => {
            setState({ ...state, updatingQuestionIndex: null, editingQuestionIndex: null })
            alert('Error updating question!')
        })

    }

    const saveExam = () => {
        setState({ ...state, savingExam: true })

        axios.post(config.api + `/course/${state.course_id}/exam/add`, {
            title: state.examTitle,
            description: state.examDescription,
            minimumPoint: state.examMinimumPoint,
            type: state.examType,
            time: state.examTime,
            orderLectureId: state.orderLectureId
        }).then(res => {
            Swal.fire('Added exam successfully!', '', 'success')
            setState({ ...state, exam_id: res.data.examId, savingExam: false })
        }).catch(err => {
            alert('Error saving exam!')
            setState({ ...state, savingExam: false })
        })
    }

    const updateExam = () => {
        setState({ ...state, savingExam: true })

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/update`, {
            title: state.examTitle,
            description: state.examDescription,
            minimumPoint: state.examMinimumPoint,
            type: state.examType,
            time: state.examTime,
            orderLectureId: state.orderLectureId
        }).then(res => {
            Swal.fire('Updated Exam!', '', 'success')
            setState({ ...state, savingExam: false })
        }).catch(err => {
            Swal.fire('Error saving exam!', '', 'error')
            setState({ ...state, savingExam: false })
        })
    }

    const removeQuestion = questionIndex => {
        const { questions } = state
        const question = questions[questionIndex]

        setState({ ...state, removingQuestion: questionIndex })

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/remove`, question).then(res => {
            delete questions[questionIndex]
            setState({ ...state, removingQuestion: null })
        }).catch(err => {
            alert('Error removing question!')
            setState({ ...state, removingQuestion: null })
        })
    }

    const addAnswer = (questionIndex) => {
        const { questions } = state
        const question = questions[questionIndex]

        let answer = {
            title: state.answerTitle,
        }

        setState({ ...state, addingAnswer: state.addingAnswerQuestionIndex })

        if (question.answerType !== 'assignment') {
            axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/answer/add`, answer).then(res => {
                answer['id'] = res.data.answerId
                question['answers'].push(answer)
                setState({ ...state, addingAnswer: null, answerTitle: '', answerCorrect: '' })
            }).catch(err => {
                alert('Error adding answer!')
                setState({ ...state, addingAnswer: null })
            })
        }

    }

    const updateQuestionCorrectAnswer = (questionIndex, answer, checked) => {
        const { questions } = state
        const question = questions[questionIndex]

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/answer/${answer.id}/correct`, {
            correct: checked
        }).then(res => {
            if (question.answerType === 'single_choice') {
                if (checked) question.singleChoiceAnswerId = answer.id
                else question.singleChoiceAnswerId = null
            }
            else if (question.answerType === 'multiple_choice') {
                if (checked && !question.multipleChoiceAnswerIds.includes(answer.id)) question.multipleChoiceAnswerIds.push(answer.id)
                else if (!checked && question.multipleChoiceAnswerIds.includes(answer.id)) question.multipleChoiceAnswerIds = question.multipleChoiceAnswerIds.filter(a => a !== answer.id)
            }
            setState({ ...state, addingAnswer: null, questions })
        }).catch(err => {
            alert('Error adding answer!')
            setState({ ...state, addingAnswer: null })
        })


    }

    const checkAnswerCorrectness = (questionIndex, answerId) => {
        let check = false

        const question = state.questions[questionIndex]

        if (question.answerType === 'single_choice' && question.singleChoiceAnswerId == answerId) check = true
        else if (question.answerType === 'multiple_choice' && question.multipleChoiceAnswerIds.includes(answerId)) check = true

        return check
    }

    const removeAnswer = (questionIndex, answerIndex) => {
        const { questions } = state
        const question = questions[questionIndex]
        const answer = question['answers'][answerIndex]

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/answer/${answer.id}/remove`, {}).then(res => {
            if (question.singleChoiceAnswerId === answer.id) question.singleChoiceAnswerId = null
            if (question.multipleChoiceAnswerIds.includes(answer.id)) question.multipleChoiceAnswerIds = question.multipleChoiceAnswerIds.filter(a => a !== answer.id)
            delete question['answers'][answerIndex]
            setState({ ...state, questions })
        }).catch(err => {
            alert('Error removing answer!')
        })

    }

    const removeQuestionAssignment = questionIndex => {
        const { questions } = state
        const question = questions[questionIndex]

        const answer = {
            title: '',
        }

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/assignment_answer/update`, answer).then(res => {
            question['assignmentAnswer'] = null
            setState({ ...state, questions })
            console.log(state.questions[questionIndex])

        }).catch(err => {
            Swal.fire('Error removing answer!', '', 'error')
        })

    }

    const updateAnswer = (questionId, questionIndex, answerIndex) => {
        const data = {
            title: state.editingAnswerTitle
        }

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${questionId}/answer/${state.editingAnswerId}/update`, data).then(res => {
            const { questions } = state
            const question = questions[questionIndex]
            question['answers'][answerIndex]['title'] = state.editingAnswerTitle

            setState({ ...state, questions, editingAnswerId: null, editingAnswerTitle: '' })
        }).catch(err => {
            Swal.fire('Error updating answer!', '', 'error')
        })
    }

    const changeAnswerTitle = (questionIndex, answerIndex, title) => {
        const { questions } = state
        const question = questions[questionIndex]
        question['answers'][answerIndex]['title'] = title

        setState({ ...state, questions })
    }

    const updateQuestionAssignment = questionIndex => {
        const question = state.questions[questionIndex]

        axios.post(config.api + `/course/${state.course_id}/exam/${state.exam_id}/question/${question.id}/assignment_answer/update`, {
            title: state.questionAssignmentAnswer
        }).then(res => {
            question['assignmentAnswer'] = state.questionAssignmentAnswer
            setState({ ...state, questionAssignmentAnswer: '' })
        }).catch(err => {
            Swal.fire('Error updating assignment answer!', '', 'error')
            // setState({ ...state, addingAnswer: null })
        })
    }

    return (
        <div style={{ width: '80%', margin: '0 auto', padding: 15 }}>

            <div style={{ position: 'relative', opacity: state.savingExam ? 0.7 : 1 }}>
                {state.savingExam && (
                    <div style={{ position: 'absolute', top: '50%', left: '50%', transform: 'translate(-50%,-50%)' }}>
                        <h2>Saving...</h2>
                    </div>
                )}
                <input
                    className="form-control"
                    type="text" placeholder="Title"
                    value={state.examTitle}
                    onChange={e => setState({ ...state, examTitle: e.target.value })}
                />
                <br />


                <input
                    className="form-control"
                    type="text" placeholder="Description"
                    value={state.examDescription}
                    onChange={e => setState({ ...state, examDescription: e.target.value })}
                />
                <br />


                <input
                    className="form-control"
                    type="text" placeholder="Minimum Point"
                    value={state.examMinimumPoint}
                    onChange={e => setState({ ...state, examMinimumPoint: e.target.value })}
                />
                <br />

                <select
                    className="form-control"
                    value={state.examType}
                    onChange={e => setState({ ...state, examType: e.target.value })}>
                    <option value="">Select Quiz Type</option>
                    <option value="time">Time</option>
                    <option value="timeless">Timeless</option>
                </select>
                <br />

                {state.examType === 'time' && (
                    <>
                        <input
                            className="form-control"
                            type="text" placeholder="Quiz Time in Minutes"
                            value={state.examTime || ''}
                            onChange={e => setState({ ...state, examTime: e.target.value })}
                        />
                        <br />
                    </>
                )}

                <select
                    className="form-control"
                    value={state.course_id || ''}
                    onChange={e => courseChange(e.target.value)}>
                    <option value="">Select Course</option>
                    {data.courses.map(course => {
                        return <option key={course.id} value={course.id}>{course.title}</option>
                    })}
                </select>
                <br />
                <select
                    className="form-control"
                    value={state.orderLectureId || ''}
                    onChange={e => setState({ ...state, orderLectureId: e.target.value })}>
                    <option value="">Exam Order</option>
                    {data.courseSections.map((section, sectionIndex) => {
                        return <optgroup key={section.id} label={section.title}>
                            {section.videos.map((video, videoIndex) => {
                                return <option key={video.id} value={video.id}>{video.title}</option>
                            })}
                        </optgroup>
                    })}
                </select>
                <br />
                {state.exam_id === null ? (
                    <button className="btn btn-success btn-block" onClick={saveExam}>Save Exam</button>
                ) : (
                        <button className="btn btn-success btn-block" onClick={updateExam}>Update Exam</button>
                    )}
            </div>

            {state.exam_id && (
                <>
                    <hr />
                    <br />
                    <h3>Questions</h3>
                    {state.questions.map((question, questionIndex) => {
                        return <div key={questionIndex}>
                            {state.editingQuestionIndex !== questionIndex && (
                                <div style={{ margin: '10px 0', display: 'flex', alignItems: 'center', justifyContent: 'space-between', padding: 5, border: '1px solid #d8d8d8', borderRadius: 4 }}>
                                    <div dangerouslySetInnerHTML={{ __html: question.title }}></div>
                                    <div>
                                        <button className="btn btn-info btn-sm mr-8pt" onClick={() => setState({ ...state, editingQuestionIndex: questionIndex, addingAnswerQuestionIndex: null })}>✎</button>
                                        <button className="btn btn-info btn-sm mr-8pt" onClick={() => setState({ ...state, addingAnswerQuestionIndex: questionIndex, editingQuestionIndex: null })}>❓</button>
                                        <button className="btn btn-danger btn-sm" onClick={() => removeQuestion(questionIndex)}>❌</button>
                                    </div>
                                </div>
                            )}
                            {(state.editingQuestionIndex === questionIndex || state.addingAnswerQuestionIndex === questionIndex) && (
                                <>
                                    <div style={{ background: '#fff', padding: 10, borderRadius: 4, margin: '40px 0' }}>
                                        {state.addingAnswerQuestionIndex !== questionIndex && (
                                            <>
                                                <h4>Edit Question</h4>
                                                <Editor menubar={false} height={115} value={question.title} handleChange={(content, editor) => updateQuestion(questionIndex, 'title', content)} />
                                                <br />

                                                <input type="text" value={question.questionVVI}
                                                    onChange={e => updateQuestion(questionIndex, 'questionVVI', e.target.value)}
                                                    className="form-control" placeholder="Question VVI" />
                                                <br />

                                                <input type="text" value={question.topic}
                                                    onChange={e => updateQuestion(questionIndex, 'topic', e.target.value)}
                                                    className="form-control" placeholder="Topic" />
                                                <br />

                                                <Editor menubar={false} height={115} value={question.explanation} handleChange={(content, editor) => updateQuestion(questionIndex, 'explanation', content)} />
                                                <br />

                                                <input type="text" value={question.explanationVVI}
                                                    onChange={e => updateQuestion(questionIndex, 'explanationVVI', e.target.value)}
                                                    className="form-control" placeholder="Explanation VVI" />
                                                <br />

                                                <select
                                                    className="form-control"
                                                    value={question.answerType}
                                                    onChange={e => updateQuestion(questionIndex, 'answerType', e.target.value)}>
                                                    <option value="">Answer Type</option>
                                                    <option value="single_choice">Single Choice</option>
                                                    <option value="multiple_choice">Multiple Choice</option>
                                                    <option value="assignment">Assignment</option>
                                                </select>
                                                <br />

                                                {state.course_id && (
                                                    <>
                                                        <select
                                                            className="form-control"
                                                            value={question.relatedLectureId}
                                                            onChange={e => updateQuestion(questionIndex, 'relatedLectureId', e.target.value)}>
                                                            <option value="">Related Lecture</option>
                                                            {data.courseSections.map((section, sectionIndex) => {
                                                                return <optgroup key={section.id} label={section.title}>
                                                                    {section.videos.map((video, videoIndex) => {
                                                                        return <option key={video.id} value={video.id}>{video.title}</option>
                                                                    })}
                                                                </optgroup>
                                                            })}
                                                        </select>
                                                        <br />
                                                    </>
                                                )}
                                            </>)}

                                        {state.addingAnswerQuestionIndex === questionIndex && <div style={{ margin: '20px 0' }}>
                                            <hr />
                                            <h3>Answers</h3>
                                            {question.answers.map((answer, answerIndex) => {
                                                return <div key={answerIndex} style={{
                                                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                                                    padding: 5, borderRadius: 4,
                                                    margin: '10px 0'
                                                }}>
                                                    <div style={{ display: 'flex', alignItems: 'center' }}>
                                                        {question.answerType !== 'assignment' && (
                                                            <>
                                                                <input type="checkbox"
                                                                    className="form-control" style={{ width: 30, height: 30, marginRight: 5 }}
                                                                    checked={checkAnswerCorrectness(questionIndex, answer.id)}
                                                                    onChange={e => updateQuestionCorrectAnswer(questionIndex, answer, e.target.checked)} />
                                                            </>
                                                        )}
                                                        {state.editingAnswerId !== answer.id && (
                                                            <div onClick={() => setState({ ...state, editingAnswerId: answer.id, editingAnswerTitle: answer.title })} dangerouslySetInnerHTML={{ __html: answer.title }}></div>
                                                        )}
                                                        {state.editingAnswerId === answer.id && <Editor menubar={false} height={115} value={state.editingAnswerTitle} handleChange={(content, editor) => setState({ ...state, editingAnswerTitle: content })} />}
                                                    </div>
                                                    <div>
                                                        {state.editingAnswerId === answer.id && <button className="btn btn-success btn-sm mr-8pt" onClick={() => updateAnswer(question.id, questionIndex, answerIndex)}>💾</button>}
                                                        <button className="btn btn-danger btn-sm" onClick={() => removeAnswer(questionIndex, answerIndex)}>❌</button>
                                                    </div>
                                                </div>
                                            })}
                                            {question.answerType !== 'assignment' &&
                                                <>
                                                    <div style={{ display: 'flex', flexDirection: 'column' }}>
                                                        <Editor menubar={false} height={115} value={state.answerTitle} handleChange={(content, editor) => setState({ ...state, answerTitle: content })} />
                                                        <div style={{ display: 'flex', alignItems: 'center', marginTop: 10 }}>
                                                            <button className="btn btn-success ml-8pt" onClick={() => addAnswer(questionIndex)}>Add Answer</button>
                                                        </div>
                                                    </div>
                                                </>
                                            }
                                            {question.assignmentAnswer && (
                                                <div style={{
                                                    display: 'flex', alignItems: 'center', justifyContent: 'space-between',
                                                    background: 'teal', color: 'white', padding: 5, borderRadius: 4,
                                                    margin: '10px 0'
                                                }}>
                                                    <div style={{ display: 'flex', alignItems: 'center' }}>
                                                        <span>{question.assignmentAnswer}</span>
                                                    </div>
                                                    <div>
                                                        <button className="btn btn-danger btn-sm" onClick={() => removeQuestionAssignment(questionIndex)}>❌</button>
                                                    </div>
                                                </div>
                                            )}
                                            {question.answerType === 'assignment' && question.assignmentAnswer == null && (
                                                <div style={{ display: 'flex', alignItems: 'center' }}>
                                                    <input type="text" className="form-control mr-8pt"
                                                        placeholder="Answer"
                                                        value={state.questionAssignmentAnswer} onChange={e => setState({ ...state, questionAssignmentAnswer: e.target.value })} />
                                                    <button className="btn btn-success ml-8pt" onClick={() => updateQuestionAssignment(questionIndex)}>+</button>
                                                </div>
                                            )}
                                            <hr />
                                            <button className="btn btn-info float-right" onClick={() => setState({ ...state, addingAnswerQuestionIndex: null })}>Close</button>
                                            <div className="clearfix"></div>
                                        </div>}

                                        {state.editingQuestionIndex === questionIndex && (
                                            <>
                                                <button className="btn btn-success float-right" onClick={saveQuestion}>💾</button>
                                                <div className="clearfix"></div>
                                            </>
                                        )}

                                    </div>
                                </>
                            )}
                        </div>
                    })}

                    {state.editingQuestionIndex === null && (
                        <div style={{ background: '#fff', padding: 10, borderRadius: 4, margin: '40px 0' }}>
                            <h4>Add Question</h4>
                            <Editor menubar={false} height={115} value={state.questionTitle} handleChange={(content, editor) => setState({ ...state, questionTitle: content })} />
                            <br />

                            <input type="text" value={state.questionVVI}
                                onChange={e => setState({ ...state, questionVVI: e.target.value })}
                                className="form-control" placeholder="Question VVI" />
                            <br />

                            <input type="text" value={state.topic}
                                onChange={e => setState({ ...state, topic: e.target.value })}
                                className="form-control" placeholder="Topic" />
                            <br />

                            Question Explanation:<br />
                            <Editor menubar={false} height={115} value={state.questionExplanation} handleChange={(content, editor) => setState({ ...state, questionExplanation: content })} />
                            <br />

                            <input type="text" value={state.explanationVVI}
                                onChange={e => setState({ ...state, explanationVVI: e.target.value })}
                                className="form-control" placeholder="Explanation VVI" />
                            <br />

                            <select
                                className="form-control"
                                value={state.questionAnswerType}
                                onChange={e => setState({ ...state, questionAnswerType: e.target.value })}>
                                <option value="">Answer Type</option>
                                <option value="single_choice">Single Choice</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="assignment">Assignment</option>
                            </select>
                            <br />

                            {state.course_id && (
                                <>
                                    <select
                                        className="form-control"
                                        value={state.relatedLectureId}
                                        onChange={e => setState({ ...state, relatedLectureId: e.target.value })}>
                                        <option value="">Related Lecture</option>
                                        {data.courseSections.map((section, sectionIndex) => {
                                            return <optgroup key={section.id} label={section.title}>
                                                {section.videos.map((video, videoIndex) => {
                                                    return <option key={video.id} value={video.id}>{video.title}</option>
                                                })}
                                            </optgroup>
                                        })}
                                    </select>
                                    <br />
                                </>
                            )}

                            <button className="btn btn-success float-right" onClick={addQuestion}>SAVE</button>
                            <div className="clearfix"></div>

                        </div>
                    )}
                </>
            )
            }

            <br />

        </div >
    );
}

if (_target) ReactDOM.render(<AddExam />, _target);

