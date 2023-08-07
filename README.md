# Тестовое задание 

Публичное API доступно по ссылке https://test-for-soft.vercel.app/api/ \
Полное описание API находится в `api.json`

### Краткое описание


GET https://test-for-soft.vercel.app/api/recipe/all \
Метод для получения всех рецептов 

GET https://test-for-soft.vercel.app/api/recipe/{id}  \
Метод для получения отдельного рецепта по его id. В путь надо передать id

DELETE https://test-for-soft.vercel.app/api/recipe/{id}  \
Метод для удаления отдельного рецепта по его id. В путь надо передать id

PATCH https://test-for-soft.vercel.app/api/recipe/{id}  \
Метод для обновления рецепта. Нужно передать объект value, с новыми значениями полей.
Если поле не передано, то оно не меняется. Но массив ingredients содержит в себе новый список ингредиентов.
Все ингредиенты вне этого списка будут удалены из рецепта, а все новые добавлены. У старых ингредиентов будет обновлено кол-во, если
оно изменилось

POST https://test-for-soft.vercel.app/api/recipe/addRecipe \
Метод для добавления нового рецепта. Обязательные поля: name, steps, path_to_photo.
Массив ingredients можно не отправлять (рецепт без ингредиентов)

POST https://test-for-soft.vercel.app/api/recipe/{1}/addImage \
Метод для добавления картинки к рецепту. Нужно сделать запрос с multipart/form-data c файлом, где имя у передаваемого поля должно быть file





GET https://test-for-soft.vercel.app/api/ingredient/all \
Метод для получения всех ингредиентов

GET https://test-for-soft.vercel.app/api/ingredient/{id}  \
Метод для получения отдельного ингредиента по его id. В путь надо передать id

DELETE https://test-for-soft.vercel.app/api/ingredient/{id}  \
Метод для удаления отдельного ингредиента по его id. В путь надо передать id

PATCH https://test-for-soft.vercel.app/api/ingredient/{id}  \
Метод для обновления ингредиента. Нужно передать объект value, с новыми значениями полей.
Если поле не передано, то оно не меняется.

POST https://test-for-soft.vercel.app/api/ingredient/addIngredient \
Метод для добавления нового ингредиента. Обязательные поля: name, unit

P.S. \
Папка `api` и файл  `vercel.json` используется для только для деплоя приложения