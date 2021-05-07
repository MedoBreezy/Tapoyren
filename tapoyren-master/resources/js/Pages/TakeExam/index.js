import React, { useState, useEffect, useRef } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import config from '../../config'

let course_id = null
let exam_id = null
let userApiToken = null

const _target = document.getElementById('react-take-exam');
if (_target) {
    userApiToken = _target.dataset.token
    exam_id = _target.dataset.examId
    course_id = _target.dataset.courseId
    axios.defaults.headers.common['Authorization'] = `Bearer ${userApiToken}`;
    axios.defaults.headers.post['Accept'] = 'application/json';
}

function useInterval(callback, delay) {
    const savedCallback = useRef();

    useEffect(() => {
        savedCallback.current = callback;
    }, [callback]);

    useEffect(() => {
        function tick() {
            savedCallback.current();
        }
        if (delay !== null) {
            let id = setInterval(tick, delay);
            return () => clearInterval(id);
        }
    }, [delay]);
}

function TakeExam() {
    const [data, setData] = useState({
        exam: {},
        translations: {},
        getData: false,
    })

    const [state, setState] = useState({
        seeResultAnswers: false,

        currentQuestionIndex: null,
        examLoaded: false,
        examStarted: false,
        examFinished: false,
        time: null,

        answers: {},
        times: {},

        examResults: {},
    });

    const currentQuestion = () => data.exam.questions[state.currentQuestionIndex]

    const addAnswer = answer => {
        const { questions } = data.exam
        const question = questions[state.currentQuestionIndex]
        const { answers } = state

        if (question.answer_type === 'assignment' || question.answer_type === 'single_choice') answers[state.currentQuestionIndex] = answer
        else if (question.answer_type === 'multiple_choice') {
            let multipleChoiceAnswers = answers[state.currentQuestionIndex]
            if (answers[state.currentQuestionIndex].includes(answer)) multipleChoiceAnswers = multipleChoiceAnswers.filter(item => item !== answer)
            else if (answers[state.currentQuestionIndex].length < question.max_answer_count) multipleChoiceAnswers.push(answer)
            answers[state.currentQuestionIndex] = multipleChoiceAnswers
        }

        setState({ ...state, answers })

    }

    const choiceCheckAnswerChecked = (answer_id, answer_type) => {
        if (answer_type === 'single_choice') return (state.answers[state.currentQuestionIndex] === answer_id)
        else if (answer_type === 'multiple_choice') return state.answers[state.currentQuestionIndex].includes(answer_id)
    }

    const startExam = () => {
        setState({ ...state, examStarted: true, currentQuestionIndex: 0 })
    }

    const finishExam = () => {
        const answers = Object.keys(state.answers).map(key => {
            const question_id = data.exam.questions[key].id
            return {
                question_id,
                answer: state.answers[key]
            }
        })

        const times = Object.keys(state.times).map(key => {
            const question_id = data.exam.questions[key].id
            return {
                question_id,
                time: state.times[key]
            }
        })

        const examData = {
            answers: JSON.stringify(answers),
            times: JSON.stringify(times)
        }

        Swal.fire({
            title: 'Exam finished!',
            html: 'Getting your results',
            onBeforeOpen: () => {
                Swal.showLoading()

                axios.post(config.api + `/course/${course_id}/exam/${exam_id}/take/submit`, examData).then(res => {
                    setState({ ...state, examResults: res.data, currentQuestionIndex: null, examStarted: false, examFinished: true })

                    // create data
                    var data = [
                        {
                            x: "Completed",
                            value: res.data.correctCount,
                            normal: {
                                fill: "dodgerblue",
                            },
                        },
                        {
                            x: "Uncompleted",
                            value: res.data.wrongCount,
                            normal: {
                                fill: "lightgray",
                            },
                        },
                    ];

                    // create a chart and set the data
                    var chart = anychart.pie(data);

                    // set the container id
                    chart.container("examResultsChart");

                    // initiate drawing the chart
                    chart.draw();

                    Swal.close()
                }).catch(err => {
                    Swal.close()
                    Swal.fire('Error getting results!', '', 'error')
                    setState({ ...state, currentQuestionIndex: null, examStarted: false, examFinished: true })
                })

            }
        })
    }

    const nextQuestion = () => {
        if (state.currentQuestionIndex + 1 < data.exam.questions.length) setState({ ...state, currentQuestionIndex: state.currentQuestionIndex + 1 })
        else finishExam()
    }
    const prevQuestion = () => {
        if (state.currentQuestionIndex > 0) setState({ ...state, currentQuestionIndex: state.currentQuestionIndex - 1 })
    }

    const submitAnswers = () => {
        Swal.fire({
            title: data.translations.are_you_sure,
            text: data.translations.no_revert,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: data.translations.yes,
            cancelButtonText: data.translations.cancel
        }).then((result) => {
            if (result.value) finishExam()
        })
    }

    const toggleResultAnswers = () => {
        setState({ ...state, seeResultAnswers: !state.seeResultAnswers })
    }

    const checkResultQuestionStatus = question_id => {
        return state.examResults.wrong_answered_questions.includes(question_id)
    }

    const checkAnswerCorrectness = (question_id, answer_type, answer) => {
        let check = false
        const correctAnswer = state.examResults.correct_question_answers.find(item => item.id === question_id)

        if (answer_type === 'single_choice' && parseInt(correctAnswer.singleChoiceAnswerId) === answer) check = true
        else if (answer_type === 'multiple_choice') {
            const correctAnswers = JSON.parse(correctAnswer.multipleChoiceAnswerIds).map(item => parseInt(item))
            if (correctAnswers.includes(answer)) check = true
        }
        // else if (answer_type === 'assignment' && correctAnswer.assignmentAnswer === answer) check = true

        return check
    }

    const checkAnswerSelected = (question_id, answer_type, answer_id) => {
        const questionIndex = data.exam.questions.findIndex(item => item.id === question_id)
        if (answer_type === 'single_choice') return state.answers[questionIndex] === answer_id
        else if (answer_type === 'multiple_choice') return state.answers[questionIndex].includes(answer_id)
    }

    const getQuestionById = id => {
        return data.exam.questions.find(question => question.id === id)
    }

    const seeExplanation = explanation => {
        Swal.fire({
            title: 'Explanation',
            html: explanation,
        })
    }

    const seeExplanationVideo = VVI => {
        Swal.fire({
            title: 'Explanation Video',
            html: `<iframe class="embed-responsive-item" src="https://player.vimeo.com/video/${VVI}" style="width: 100%; height: 350px;" allowfullscreen></iframe>`,
        })
    }

    const estimatedTime = () => {
        const currentTime = state.time

        let minutes = parseInt(currentTime / 60) // 1
        let seconds = parseInt(currentTime - (minutes * 60))

        if (minutes < 10) minutes = `0${minutes}`
        if (seconds < 10) seconds = `0${seconds}`

        return [minutes, seconds]
    }


    useInterval(() => {
        if (state.examLoaded && state.examStarted && !state.examFinished && state.currentQuestionIndex !== null && state.time > 0) {
            const { times } = state
            times[state.currentQuestionIndex] = times[state.currentQuestionIndex] + 1
            setState({ ...state, time: state.time - 1, times })
        }
        if (!state.examFinished && state.time === 0) {
            finishExam()
        }
    }, 1000)

    useEffect(() => {
        if (!data.getData) {
            if (Object.keys(data.exam).length === 0) axios.get(config.api + `/course/${course_id}/exam/${exam_id}/take/data`).then(res => {
                const answers = {}
                const times = {}

                res.data.questions.forEach((question, i) => {
                    times[i] = 0
                    if (question.answer_type === 'assignment') answers[i] = ''
                    else if (question.answer_type === 'single_choice') answers[i] = null
                    else if (question.answer_type === 'multiple_choice') answers[i] = []
                })

                const calculatedTime = res.data.time ? (parseInt(res.data.time) * 60) : null

                setData({ ...data, exam: res.data })
                setState({ ...state, examLoaded: true, answers, times, time: calculatedTime })

            }).catch(err => {
                Swal.fire('Error getting exam data!', '', 'error')
            })

            if (Object.keys(data.exam).length > 0 && Object.keys(data.translations).length === 0) axios.get(config.api + `/translations`).then(res => {
                setData({ ...data, translations: res.data.translations })
            });

            setData({ ...data, getData: true })
        }
    }, [data.getData])

    return (
        <div className="exam_content">
            {(state.examLoaded && !state.examStarted && !state.examFinished) && (
                <div>

                    <div className="exam-start">
                        <h1 className="text-white flex m-0">{data.exam.questions.length} {data.translations.exam_questions}</h1>
                        {state.time !== null && <p className="exam-start-time">{data.translations.time}: {estimatedTime()[0]}:{estimatedTime()[1]}</p>}

                        <h2>{data.translations.are_you_ready}</h2>
                        <button onClick={startExam}>{data.translations.start_exam}</button>
                    </div>
                </div>
            )}
            {(state.examLoaded && state.examStarted && state.currentQuestionIndex !== null && !state.examFinished) && (
                <>
                    <div className="question_navigation">
                        {data.exam.questions.map((question, qi) => {
                            return <div key={qi} onClick={() => setState({ ...state, currentQuestionIndex: qi })} className={`question${state.currentQuestionIndex === qi ? ' active' : ''}`}>{qi + 1}</div>
                        })}
                    </div>
                    <h2>{data.translations.question} {state.currentQuestionIndex + 1} - {data.exam.questions.length}</h2>

                    {state.time && <h3 className="time">{estimatedTime()[0]}:{estimatedTime()[1]}</h3>}

                    {currentQuestion().question_vimeoVideoId && (
                        <iframe id="lesson-player" className="embed-responsive-item" src={`https://player.vimeo.com/video/${currentQuestion().question_vimeoVideoId}`} style={{ width: '100%', height: 300 }} allowfullscreen></iframe>
                    )}
                    <h3 className="question" dangerouslySetInnerHTML={{ __html: currentQuestion().title }}></h3>

                    <div className="exam_answers">
                        {currentQuestion().answer_type === 'assignment' && (
                            <input
                                value={state.answers[state.currentQuestionIndex]}
                                placeholder={data.translations.type_your_answer}
                                onChange={e => addAnswer(e.target.value)}
                            />
                        )}

                        {currentQuestion().answer_type !== 'assignment' && (
                            <div className="checkboxes_wrapper">
                                {currentQuestion().answers.map((answer, i) => {
                                    return (
                                        <div className="checkbox" key={i} onClick={() => addAnswer(answer.id)}>
                                            <i className="material-icons">{choiceCheckAnswerChecked(answer.id, currentQuestion().answer_type) ? 'done' : ''}</i>
                                            <div dangerouslySetInnerHTML={{ __html: answer.title }}></div>
                                        </div>
                                    )
                                })}
                            </div>
                        )}

                        {currentQuestion().answer_type === 'multiple_choice' && <p style={{ marginTop: 40 }}>{data.translations.multiple_choice_note}</p>}
                    </div>

                    <div className="buttons">
                        {state.currentQuestionIndex > 0 && <button onClick={prevQuestion} className="prev">{data.translations.previous}</button>}
                        {state.currentQuestionIndex + 1 < data.exam.questions.length && (
                            <button onClick={nextQuestion} className="next">{data.translations.next}</button>
                        )}
                        {state.currentQuestionIndex + 1 === data.exam.questions.length && (
                            <button onClick={submitAnswers} className="next">{data.translations.submit}</button>
                        )}
                    </div>

                </>
            )}
            {(!state.examStarted && state.examFinished && Object.keys(state.examResults).length > 0) && (
                <div className="exam-results">

                    <div className="head">
                        <h1 className="status">{data.translations.exam_status}: {state.examResults.exam_status === 'passed' ? (
                            <span style={{ color: '#73B508' }}>{data.translations.passed}</span>
                        ) : (
                                <span style={{ color: 'red' }}>{data.translations.failed}</span>
                            )}</h1>

                        <div id="examResultsChart" style={{ width: '100%', height: '350px', margin: '20px 0' }}></div>

                        <h2>{data.translations.total_exam_time}: {state.examResults.total_exam_time} seconds</h2>
                        <h2>{data.translations.minimum_score}: {state.examResults.minimum_point_percent}%</h2>
                        <h2>{data.translations.your_score}: {state.examResults.student_point_percent}%</h2>

                        <div className="see-answers">
                            <button onClick={toggleResultAnswers}>{data.translations.see_answers}</button>
                        </div>
                    </div>

                    {state.seeResultAnswers && (
                        <div>

                            <div className="divider"></div>

                            {data.exam.questions.map((question, questionIndex) => {
                                const isWrong = checkResultQuestionStatus(question.id)
                                return <div key={"question_" + questionIndex} className={`result-question${isWrong ? ' wrong' : ''}`}>
                                    <h3 style={{ margin: 0 }} dangerouslySetInnerHTML={{ __html: question.title }}></h3>
                                    <div className="answers">
                                        {question.answer_type !== 'assignment' && question.answers.map((answer, answerIndex) => {
                                            const isCorrect = checkAnswerCorrectness(question.id, question.answer_type, answer.id)
                                            const isSelected = checkAnswerSelected(question.id, question.answer_type, answer.id)
                                            return (
                                                <div className={`exam-answer${isCorrect ? ' correct-answer' : ''}`} key={"answer_" + answerIndex}>
                                                    <i className="material-icons checkbox">{isSelected ? 'done' : ''}</i>
                                                    <div className="answer" dangerouslySetInnerHTML={{ __html: answer.title }}></div>
                                                </div>
                                            )
                                        })}
                                        {question.answer_type === 'assignment' && (
                                            <>
                                                {isWrong ? (
                                                    <>
                                                        <div className="exam-answer">
                                                            {state.answers[data.exam.questions.findIndex(item => item.id === question.id)]}
                                                        </div>
                                                        <div className="exam-answer correct-answer">
                                                            {state.examResults.correct_question_answers.find(item => item.id === question.id).assignmentAnswer}
                                                        </div>
                                                    </>
                                                ) : (
                                                        <div className="exam-answer correct-answer">
                                                            {state.answers[data.exam.questions.findIndex(item => item.id === question.id)]}
                                                        </div>
                                                    )}
                                            </>
                                        )}
                                    </div>
                                    <div className="bottom">
                                        <div className="see-explanation">
                                            {getQuestionById(question.id).explanation && <button onClick={() => seeExplanation(getQuestionById(question.id).explanation)}>{data.translations.see_explanation}</button>}
                                            {getQuestionById(question.id).explanation_VVI && <button onClick={() => seeExplanationVideo(getQuestionById(question.id).explanation_VVI)}>{data.translations.see_explanation_video}</button>}
                                        </div>
                                        <div className="related-lecture">
                                            {data.translations.related_lecture}: <a href={`${config.host}/course/${course_id}/lesson/${getQuestionById(question.id).relatedLectureId}`} target="_blank">{getQuestionById(question.id).relatedLectureTitle}</a>
                                        </div>
                                    </div>
                                </div>
                            })}


                        </div>
                    )}

                </div>
            )}
        </div>
    )

}


if (_target) ReactDOM.render(<TakeExam />, _target);

