import './js/app';
import './scss/style.scss';
import './bootstrap';
import 'flag-icons/css/flag-icons.css';

const imagesContext = require.context('./images', true, /\.(jpg|jpeg|png|svg|gif)$/);
imagesContext.keys().forEach(imagesContext);
