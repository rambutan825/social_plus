import CertificationBase from '../component/Certification';
import Manager from '../component/certification/Manage';
import CategoryEdit from '../component/certification/CategoryEdit';
import Certification from  '../component/certification/Certification';
import CertificationEdit from '../component/certification/CertificationEdit';
import CertificationAdd from '../component/certification/CertificationAdd';
const routers = {
    path: 'certifications',
    component: CertificationBase,
    children: [
        {
            path: '',
            name:'certification:users',
            component: Certification,
        },
        {
            path: 'categoies',
            name: 'certification:categoies',
            component: Manager
        },
        {
            path: 'category/:name/edit',
            name: 'certification:category:edit',
            component: CategoryEdit
        },
        {
            path: 'add',
            name: 'certification:add',
            component: CertificationAdd,
        },
        {
            path: ':certification',
            name:'certification:edit',
            component: CertificationEdit,
        },
    ]
};

export default routers;