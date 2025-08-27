
########フォルダにある画像名を全部、配列にいれる。

import wupy_model


from chainer.cuda import to_cpu
retval = []
id = ''
school = ''

import glob
test_image_url_array =[]
files = glob.glob("/var/www/html/shinzemi/ancake/images/*.jpg")#正規表現　[a-z_]-\d数字指定か文字列指定
for file in files:
    if 'id' in file:#アンケードIDを識別
        id = file
    elif 'school' in file:#学年判定 ファイル名にschoolってはいってるかどうか
        school = file
    else:
        test_image_url_array += [file]


teacher_label = check_ID( id )
id_name = id.replace('/var/www/html/shinzemi/ancake/images/','').replace('.jpg','')#ファイル名部分のみにする
retval.append([ id_name,teacher_label ])  # アンケートIDの値


# teacher_label = check_ID( school )
# school_name = school.replace('/var/www/html/shinzemi/ancake/images/','').replace('.jpg','')#ファイル名部分のみにする
# school_data= convert_test_dataGaku(school, (INPUT_WIDTH_GAKU, INPUT_HEIGHT_GAKU))
# with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
#     teacher_labels = modelGaku.predictor(school_data)
#     teacher_labels = to_cpu(teacher_labels.array)
#     teacher_label = teacher_labels.argmax(axis=1)[0]
#     retval.append([ school_name,teacher_label ])  # 学年の値





i = 0
for test_image_url in test_image_url_array:
    image_name = test_image_url.replace('/var/www/html/shinzemi/ancake/images/','').replace('.jpg','')#ファイル名部分のみにする
    # image_name = test_image_url.replace('/var/www/html/shinzemi/ancake/images/','').replace('.jpg','')#ファイル名部分のみにする

    if 'id' in image_name:#アンケードIDを識別
       teacher_label = check_ID( test_image_url )
       retval.append([ image_name,teacher_label ])  # アンケートIDの値
        
    elif 'school' in image_name:#学年判定 ファイル名にschoolってはいってるかどうか
        print(image_name)
        test_data= convert_test_dataGaku(test_image_url, (INPUT_WIDTH_GAKU, INPUT_HEIGHT_GAKU))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
            teacher_labels = modelGaku.predictor(test_data)
            teacher_labels = to_cpu(teacher_labels.array)
            teacher_label = teacher_labels.argmax(axis=1)[0]
            retval.append([ image_name,teacher_label ])  # レベルが値だっぺ


    elif 'class' in image_name:#ABCDE判別 ファイル名にclassってはいってるかどうか
        test_data= convert_test_dataABC(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
          teacher_labels = modelABC.predictor(test_data)
          teacher_labels = to_cpu(teacher_labels.array)
        #  print(teacher_labels)
          teacher_label = teacher_labels.argmax(axis=1)[0]
          
          if teacher_label == 0:
            retval.append([ image_name,0 ]) # レ点なし
          elif teacher_label == 1:
            retval.append([ image_name,'A' ]) # 1にレ点あり
          elif teacher_label == 2:
            retval.append([ image_name,'B' ]) # 2にレ点あり
          elif teacher_label == 3:
            retval.append([ image_name,'C' ]) # 3にレ点あり
          elif teacher_label == 4:
            retval.append([ image_name,'D' ]) # 4にレ点あり
          elif teacher_label == 5:
            retval.append([ image_name,'E' ]) # 5にレ点あり
          else :
            retval.append([ image_name,'' ]) #  #''
    else:
        test_data= convert_test_data(test_image_url, (INPUT_WIDTH, INPUT_HEIGHT))
        with chainer.using_config('train', False), chainer.using_config('enable_backprop', False):
          teacher_labels = model.predictor(test_data)
          teacher_labels = to_cpu(teacher_labels.array)
        #  print(teacher_labels)
          teacher_label = teacher_labels.argmax(axis=1)[0]
          
          retval.append([ image_name,teacher_label ]) # レ点なし
    
    i = i + 1


print(retval)









