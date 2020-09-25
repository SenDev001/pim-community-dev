import React, {FC} from 'react';
import {PimView} from '@akeneo-pim-community/legacy-bridge';

const AttributeGroupsCreateButton: FC = () => {
    return (
       <PimView
           className=''
           viewName='pim-attribute-group-index-create-button'
       />
   );
};

export {AttributeGroupsCreateButton};
