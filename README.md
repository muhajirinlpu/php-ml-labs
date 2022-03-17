PHP Machine Learning Lab
-

This program build using [Laravel Zero](https://laravel-zero.com/) and [Rubix ML](https://rubixml.com/).

### Naive Bayes (Rubix ML)
```shell
$ php application demo:naive-bayes data/dataset.csv data/test.csv --name="Possibility to buy a laptop"
```
Result :

https://user-images.githubusercontent.com/13056772/158528008-ea73da58-f544-42ff-95e7-47ba06839612.mp4


### Decision Tree (No Rubix ML)
```shell
php application demo:decision-tree data/signal.csv
```
Result : 

![Kooha-03-17-2022-21-43-57](https://user-images.githubusercontent.com/13056772/158830083-0e6999ea-f166-4a1a-88de-325624c9561a.gif)


```shell
php application demo:decision-tree data/heart_decease.csv
```
![Kooha-03-17-2022-21-44-24](https://user-images.githubusercontent.com/13056772/158830692-9eb6cff9-aeb8-461b-a867-da29d7f054bd.gif)
