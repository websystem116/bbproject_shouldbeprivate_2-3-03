



#import cupy
import chainer
from PIL import Image
import numpy as np
#import matplotlib.pyplot as plt

 
INPUT_WIDTH = 128 #32
INPUT_HEIGHT = 36 #32

def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

import cv2
#import matplotlib.pyplot as plt


import chainer
import chainer.functions as F
import chainer.links as L



from chainer import training,serializers,Chain,datasets,sequential,optimizers,iterators
#from chainer.training import extensions,Trainer
#GPU_ID = 0
#BATCH_SIZE = 16#初期64    レ点２
#MAX_EPOCH = 14# 初期１０　レ点８


####ABCの学習モデルLoad
##CNNを設定する　ニューラルネットワーク　ABC判定用
class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 6)
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func


modelABC = L.Classifier(CNN())
####ABC判定 End


####学年の学習モデルLoad

from chainer import serializers
serializers.load_hdf5("/var/www/html/shinzemi/ancake/chainer-dogscatsABC-model.h5", modelABC)

##CNNを設定する　ニューラルネットワーク　設問判定用
class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 5)
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func


model = L.Classifier(CNN())


#モデルのロード
from chainer import serializers
serializers.load_hdf5("var/www/html/shinzemi/ancake/chainer-dogscats-model.h5", model)

####設問判定 End



####学年の学習モデルLoad

INPUT_WIDTH_GAKU = 1160 #32   128
INPUT_HEIGHT_GAKU = 160 #32    36
##CNNを設定する　ニューラルネットワーク　学年判定用
class CNN(Chain):
  # コンストラクタ
  def __init__(self):
    super(CNN, self).__init__()

    with self.init_scope():
      self.conv1 = L.Convolution2D(None, out_channels=32, ksize=3, stride=1, pad=1)
      self.conv2 = L.Convolution2D(in_channels=32, out_channels=64, ksize=3, stride=1, pad=1)
      self.conv3 = L.Convolution2D(in_channels=64, out_channels=128, ksize=3, stride=1, pad=1)
      self.conv4 = L.Convolution2D(in_channels=128, out_channels=256, ksize=3, stride=1, pad=1)
      self.layer1 = L.Linear(None, 1000)
      self.layer2 = L.Linear(1000, 14)#　最後は１３個のどれかに振り分け
  #
  def __call__(self, input):
    func = F.max_pooling_2d(F.relu(self.conv1(input)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv2(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv3(func)), ksize=2, stride=2)
    func = F.max_pooling_2d(F.relu(self.conv4(func)), ksize=2, stride=2)
    func = F.dropout(F.relu(self.layer1(func)), ratio=0.80)
    func = self.layer2(func)
    return func


modelGaku = L.Classifier(CNN())

#モデルのロード
from chainer import serializers
serializers.load_hdf5("/var/www/html/shinzemi/ancake/chainer-dogscatsGaku-model.h5", modelGaku)




###############################
#関数の定義　
#

def data_reshape(width_height_channel_image):
  image_array = np.array(width_height_channel_image)
  return image_array.transpose(2, 0, 1)

def convert_test_dataABC(image_file_path, size, show=False):#ABC判別用
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH,INPUT_HEIGHT),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = modelABC.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result
  
def convert_test_data(image_file_path, size, show=False):#設問判別用
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH,INPUT_HEIGHT),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = model.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result

  
def convert_test_dataGaku(image_file_path, size, show=False):#設問判別用
  image = Image.open(image_file_path)
  result_image = image.resize((INPUT_WIDTH_GAKU,INPUT_HEIGHT_GAKU),Image.LANCZOS)

  # 画像データをChainerのConvolution2Dに使えるように整備します
  image = data_reshape(result_image)
  # 型をfloat32に変換します
  result = image.astype(np.float32)
  # 学習済みモデルに渡します
  result = model.xp.asarray(result)
  # モデルに渡すデータフォーマットに変換します
  result = result[None, ...]
  return result
  
  
################アンケートID判定############### 
def check_ID( path ):
    import os
    os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "/var/www/html/shinzemi/ancake/kimura01-a1eecf5c15f4.json"
    import platform

    #対象画像の読み込み
    import sys
    args = sys.argv
    image_file = path

    import io
    from google.cloud import vision
    client = vision.ImageAnnotatorClient()

    with io.open(image_file,'rb') as image_file:
        content = image_file.read()
    image = vision.Image(content=content)

    #APIに投げる
    response = client.document_text_detection(image=image)
    document = response.full_text_annotation

    mojiretu = ''

    for page in document.pages:
        for block in page.blocks:
            for paragraph in block.paragraphs:      
                for word in paragraph.words:
                    for symbol in word.symbols:
                        mojiretu = mojiretu + symbol.text   #配列　文字自体
        
    return(mojiretu)
  
  
  
################アンケートID判定 END###########  
  
  
  
  
