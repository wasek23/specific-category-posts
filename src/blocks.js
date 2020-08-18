//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType('wasek/specific-category-posts', {
    // Build In attributes
    title: 'Category Posts',
    icon: 'editor-table',
    category: 'common',
    keywords: ['Specific Category Posts'],

    // Custom Attributes
    attributes: {
        categories: { type: 'object' },
        selectedCategory: { type: 'string' },
        postsPerPage: { type: 'string' }
    },

    // Custom Functions
    edit: props => {
        const { attributes: { categories, selectedCategory, postsPerPage }, setAttributes } = props;

        !categories && wp.apiFetch({
            url: cgbGlobal.siteUrl + '/wp-json/wp/v2/categories'
        }).then(cats => {
            setAttributes({ categories: cats })
        })

        if (!categories) {
            return 'Loading...';
        }
        if (categories && categories.length === 0) {
            return 'No categories found. Please add some!';
        }

        return (
            <div className='grid2'>
                <label htmlFor='selectedCategory'>
                    <strong>Select category: </strong>

                    <select value={selectedCategory} onChange={e => { setAttributes({ selectedCategory: e.target.value }) }} id='selectedCategory'>
                        <option>Select Category</option>
                        {
                            categories.map(category => {
                                return <option value={category.id} key={category.id}>{category.name}</option>;
                            })
                        }
                    </select>
                </label>

                <label htmlFor='postsPerPage'>
                    <strong>Post per page: </strong>

                    <input type='number' value={postsPerPage} onChange={e => { setAttributes({ postsPerPage: e.target.value }) }} placeholder='Post per page' min='1' id='postsPerPage' />
                </label>
            </div>
        );
    },

    save: () => null
});