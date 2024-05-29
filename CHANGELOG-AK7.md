## Added default Label & Image properties

He now have default properties that reflect Reference Entities...

You have to enable them to use them...

Here is an example with Color

```yaml
## grid example color
# */ReferenceDataBundle/Resources/config/datagrid/color.yml

datagrid:
  color:
    options:
      entityHint: color
      manageFilters: false
    source:
      type: pim_datasource_default
      entity: Induxx\Bundle\ReferenceDataBundle\Entity\ReferenceIcon
      repository_method: createDatagridQueryBuilder
    columns:
      code:
        label: pim_custom_entity.form.tab.code.title
      label:
        label: pim_custom_entity.form.tab.label.title
    filters:
      columns:
        code:
          type: string
          label: pim_custom_entity.form.tab.code.title
          data_name: rd.code
        label:
          type: search
          label: pim_custom_entity.form.tab.label.title
          data_name: rd.label
    sorters:
      columns:
        code:
          data_name: rd.code
        label:
          data_name: rd.label
      default:
        code: '%oro_datagrid.extension.orm_sorter.class%::DIRECTION_ASC'
    properties:
      id: ~
      edit_link:
        type: url
        route: pim_customentity_rest_get
        params:
          - id
          - customEntityName
      delete_link:
        type: url
        route: pim_customentity_rest_delete
        params:
          - id
          - customEntityName
    actions:
      edit:
        type: navigate
        label: grid.action.label.edit
        icon: edit
        link: edit_link
        rowAction: true
      delete:
        type: delete
        label: grid.action.label.delete
        icon: trash
        link: delete_link
```

```shell
## add label and image
# */ReferenceDataBundle/Resources/config/form_extensions/color/edit.yml
...
  <project>-color-edit-form-properties-label:
    module: pim/form/common/fields/text
    parent: <project>-color-edit-form-properties-common
    targetZone: content
    position: 90
    config:
      fieldName: label
      label: pim_custom_entity.form.tab.label.title

  <project>-color-edit-form-properties-image:
    module: referencedata/media-field
    parent: <project>-color-edit-form-properties-common
    targetZone: content
    position: 130
    config:
      fieldName: image
      label: pim_custom_entity.form.tab.image.title
      required: false
      readOnly: false
```

```yaml
# */ReferenceDataBundle/Resources/config/doctrine/Color.orm.yml
Induxx\Bundle\ReferenceDataBundle\Entity\Color:
  type: entity
  table: refdata_reference_color
  changeTrackingPolicy: DEFERRED_EXPLICIT
  repositoryClass: Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository
  fields:
    id:
      type: integer
      id: true
      generator:
        strategy: AUTO
    code:
      type: string
      length: 255
      nullable: false
      unique: true
    label:
      type: string
      length: 255
      nullable: true
  oneToOne:
    image:
      targetEntity: Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface
      joinColumn:
        name: Image
        referencedColumnName: id
        onDelete: 'SET NULL'
      cascade:
        - persist
```
After this you will have to generate a custom DB Integration which is a Case by Case Scenario.
Some might already have and Image or a Label ...