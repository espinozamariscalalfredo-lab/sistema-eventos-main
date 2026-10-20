from PIL import Image, ImageDraw
import glob, os, math
files=sorted(glob.glob('tmp/pdfs/revision-*.png'))
thumbs=[]
for f in files:
    im=Image.open(f).convert('RGB')
    im.thumbnail((210,300))
    tile=Image.new('RGB',(220,320),'#dddddd')
    tile.paste(im,((220-im.width)//2,5))
    ImageDraw.Draw(tile).text((8,302),os.path.basename(f),fill='black')
    thumbs.append(tile)
out=Image.new('RGB',(880,320*math.ceil(len(thumbs)/4)),'white')
for i,im in enumerate(thumbs): out.paste(im,((i%4)*220,(i//4)*320))
out.save('tmp/pdfs/contact-sheet.png')
