from PIL import Image, ImageDraw
import glob, os, math
files=sorted(glob.glob('tmp/pdfs/guia_preview-*.png'))
thumbs=[]
for f in files:
    im=Image.open(f).convert('RGB')
    im.thumbnail((220,310))
    tile=Image.new('RGB',(230,332),'#e5e7eb')
    tile.paste(im,((230-im.width)//2,4))
    ImageDraw.Draw(tile).text((8,315),os.path.basename(f),fill='black')
    thumbs.append(tile)
out=Image.new('RGB',(920,332*math.ceil(len(thumbs)/4)),'white')
for i,im in enumerate(thumbs): out.paste(im,((i%4)*230,(i//4)*332))
out.save('tmp/pdfs/guia_contact_sheet.png')
print(f'Páginas renderizadas: {len(files)}')
