# JS Component

The module provides a streamlined solution for adding simple React or JS based components to the Drupal ecosystem. The module is more developer centric, as it allows developers to define JS components by exposing a plugin or by placing a YAML file within the module or theme directory.

The `libraries` directive follows the same syntax as what you would expect in a `*.libraries.yml`. The `settings` directive allows you to collect information from the site-builder. It follows the same structure as the Drupal form API. Inputted settings are exposed to the JS component using the `drupalSettings` concept.

Below is an example of what a JS component YAML base definition looks like. 


`[THEME/MODULE].js_component.yml`

```
component_1:
  label: My React Component
  description: This app will run the world!
  libraries:
    js:
      /js_component/react-app/build/static/js/main.ca4c6d6d.js: {}
    css:
      theme:
        /js_component/react-app/build/static/css/main.666d445f.css: {}
  settings:
    collect_user_input:
      type: 'select'
      title: 'User Input'
      description: 'Any data you would like to provide to the react application.'
      options:
        option-1: Option 1
        option-2: Option 2
      empty_option: '- Select -'

```

