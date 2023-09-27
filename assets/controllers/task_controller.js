import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['description']
    
    connect() {
        const { height } = this.descriptionTarget.ownerDocument.defaultView.getComputedStyle(this.descriptionTarget, null);
        this.height = height;
        this.descriptionTarget.style.height = '0px';
    }

    toggle() {
        const el = this.descriptionTarget;
        el.style.transition = 'height 0.3s';
        const { height } = el.ownerDocument.defaultView.getComputedStyle(el, null);
        if (parseInt(height, 10) === 0) {
            el.style.height = this.height;
        } else {
            el.style.height = '0px';
        }
    }
}
