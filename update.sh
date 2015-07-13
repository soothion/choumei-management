git checkout choumei-management-api
git pull
git checkout master
git merge choumei-management-api
git checkout choumei-management-ui
git pull
git checkout master
git merge choumei-management-ui
cd api
apidoc -i app/Http/Controllers -o public/doc
