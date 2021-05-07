import React, { useState, useEffect } from "react";
import ReactDOM from 'react-dom'

import axios from 'axios'

import config from '../../config'

const _target = document.getElementById('react-modal-categories');

function ModalCategories() {

    const [data, setData] = useState({
        getData: false,
        categories: []
    })

    const [state, setState] = useState({
        activeCategory: null,
        activeSubCategory: null,
    });

    useEffect(() => {
        if (!data.getData) {
            if (data.categories.length === 0) axios.get(config.api + '/categories/all_data').then(res => {
                if (res.data.categories.length > 0) setData({ ...data, categories: [...res.data.categories] })
            })
            setData({ ...data, getData: true })
        }
    }, [data.getData])

    const selectCategory = i => setState({ ...state, activeCategory: i, activeSubCategory: null })
    const selectSubCategory = i => setState({ ...state, activeSubCategory: i })

    return (
        <div className="modal-categories-wrapper">

            <div className="categories" style={state.activeSubCategory ? {} : { borderRight: 0 }}>
                {data.categories.map((category, i) => {
                    return <h4 onClick={() => selectCategory(i)} key={i}>{category.title}</h4>
                })}
            </div>
            {state.activeCategory !== null && (
                <div className="subCategories">
                    {state.activeCategory !== null && data.categories[state.activeCategory].sub_categories.map((subcat, si) => {
                        return <h4 onClick={() => selectSubCategory(si)} key={si}>{subcat.title}</h4>
                    })}
                </div>
            )}
            {state.activeSubCategory !== null && (<div className="courses">
                {state.activeCategory !== null && state.activeSubCategory !== null && data.categories[state.activeCategory].sub_categories[state.activeSubCategory].courses.map((course, i) => {
                    return <div className="course" key={i} onClick={() => location.replace(`https://tapoyren.com/course/${course.id}`)}>
                        <img src={course.thumbnail_url} />
                        <h5>{course.title}</h5>
                    </div>
                })}
            </div>
            )}


        </div>
    );
}

if (_target) ReactDOM.render(<ModalCategories />, _target);

