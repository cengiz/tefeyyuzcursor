---
title: "Yapay Zekâ"
slug: "yapay-zeka"
description: "Yapay zekâ nedir, nasıl çalışır? Tarihçesi, makine öğrenmesi ve derin öğrenme ile ilişkisi, büyük dil modelleri ve etik tartışmalar üzerine kapsamlı Türkçe ansiklopedi maddesi."
date: 2026-10-05T14:00:00+03:00
lastmod: 2026-10-16T14:00:00+03:00
kategoriler: ["Teknoloji"]
etiketler: ["yapay zekâ", "makine öğrenmesi", "derin öğrenme", "büyük dil modelleri"]
keywords: ["yapay zeka nedir", "yapay zeka tarihi", "makine öğrenmesi nedir", "derin öğrenme", "LLM"]
ozet: "Yapay zekâ (YZ), bilgisayar sistemlerinin öğrenme, akıl yürütme ve problem çözme gibi insan zekâsına özgü yetenekleri taklit etmesini amaçlayan bilim dalıdır. 1956'da adlandırılan alan, 2010'lardan itibaren derin öğrenme ile büyük bir sıçrama yaşadı."
oneCikan: true
kaynaklar:
  - "Stuart Russell & Peter Norvig, *Artificial Intelligence: A Modern Approach*, 4. baskı, Pearson, 2020."
  - "Alan Turing, \"Computing Machinery and Intelligence\", *Mind*, 1950."
  - "Ian Goodfellow, Yoshua Bengio, Aaron Courville, *Deep Learning*, MIT Press, 2016."
---

**Yapay zekâ** (kısaca **YZ**; İngilizce *artificial intelligence*, AI), makinelerin insan zekâsıyla ilişkilendirilen öğrenme, algılama, akıl yürütme, dil anlama ve karar verme gibi yetenekleri sergilemesini inceleyen ve geliştiren bilgisayar bilimi dalıdır.

## Tanım ve Kapsam

Alanın tek bir tanımı yoktur. Russell ve Norvig'in sınıflandırmasına göre yapay zekâ yaklaşımları dört eksende toplanabilir: **insan gibi düşünen**, **insan gibi davranan**, **akılcı düşünen** ve **akılcı davranan** sistemler. Günümüzde en yaygın kabul gören çerçeve, çevresini algılayıp hedeflerine ulaşma olasılığını en yükselten eylemleri seçen **akılcı etmen** (rational agent) anlayışıdır.

Yapay zekâ genellikle iki ana kategoride değerlendirilir:

- **Dar (zayıf) yapay zekâ:** Tek bir görevde uzmanlaşmış sistemler; satranç programları, öneri motorları, yüz tanıma.
- **Genel (güçlü) yapay zekâ:** İnsan düzeyinde her alanda öğrenip akıl yürütebilen, henüz gerçekleştirilememiş kuramsal sistemler.

## Tarihçe

### Öncüler (1940–1955)

Alanın felsefî temelleri Aristoteles'in mantığına kadar götürülse de modern anlamda yapay zekâ, **Alan Turing**'in 1950 tarihli makalesiyle başlar. Turing, "Makineler düşünebilir mi?" sorusunu, bugün **Turing Testi** olarak bilinen bir taklit oyunuyla ele almıştır.

### Doğuş ve ilk heyecan (1956–1974)

"Yapay zekâ" terimi, **1956 Dartmouth Konferansı**'nda John McCarthy tarafından önerildi. Marvin Minsky, Allen Newell ve Herbert Simon gibi isimler, sembolik akıl yürütmeye dayalı ilk programları geliştirdi. Dönemin iyimser tahminleri (örneğin yirmi yıl içinde makinelerin her işi yapabileceği) gerçekleşmedi.

### Yapay zekâ kışları

1970'lerde ve 1980'lerin sonunda, vaatlerin karşılanamaması nedeniyle fonların kesildiği **yapay zekâ kışları** yaşandı. Bu dönemlerde yine de **uzman sistemler** sanayide uygulama alanı buldu.

### İstatistiksel dönüş ve derin öğrenme (1990–günümüz)

1990'lardan itibaren alan, kurallara dayalı yaklaşımdan veriye dayalı **makine öğrenmesine** yöneldi. 1997'de IBM'in Deep Blue'su dünya satranç şampiyonu Garry Kasparov'u yendi. Asıl kırılma, 2012'de **AlexNet**'in görüntü tanıma yarışmasında derin sinir ağlarıyla elde ettiği başarıdır. 2017'de önerilen **Transformer** mimarisi ise bugünkü **büyük dil modellerinin** (LLM) temelini oluşturdu.

## Temel Yaklaşımlar

### Makine öğrenmesi

Makine öğrenmesi, sistemin açıkça programlanmadan veriden örüntüler öğrenmesidir. Üç ana türü vardır:

1. **Gözetimli öğrenme:** Etiketli örneklerden öğrenme (ör. spam tespiti).
2. **Gözetimsiz öğrenme:** Etiketsiz veride yapı bulma (ör. müşteri kümeleme).
3. **Pekiştirmeli öğrenme:** Ödül-ceza mekanizmasıyla deneme-yanılma (ör. oyun oynayan ajanlar).

### Derin öğrenme

Çok katmanlı **yapay sinir ağları** kullanan derin öğrenme, görüntü, ses ve metin gibi karmaşık verilerde insan performansına yaklaşan ya da onu aşan sonuçlar üretmiştir. Başarısında büyük veri kümeleri, GPU hesaplama gücü ve algoritmik yenilikler belirleyici olmuştur.

### Büyük dil modelleri

Milyarlarca parametreli, devasa metin külliyatıyla eğitilmiş bu modeller; metin üretme, özetleme, çeviri ve kod yazma gibi görevleri tek bir sistemle yerine getirebilir. **Üretken yapay zekâ** (generative AI) teriminin yaygınlaşması bu modellerle olmuştur.

{{< bilgi baslik="Dikkat" tur="uyari" >}}
Büyük dil modelleri, kendinden emin biçimde **yanlış bilgi** üretebilir ("halüsinasyon"). Bu nedenle ürettikleri içerik, özellikle sağlık, hukuk ve finans gibi alanlarda mutlaka güvenilir kaynaklarla doğrulanmalıdır.
{{< /bilgi >}}

## Uygulama Alanları

Yapay zekâ bugün sağlıkta görüntü tabanlı tanı, finansta dolandırıcılık tespiti, ulaşımda otonom araçlar, tarımda verim tahmini, eğitimde kişiselleştirilmiş öğrenme ve günlük hayatta sesli asistanlar ile öneri sistemleri gibi sayısız alanda kullanılmaktadır.

## Etik ve Toplumsal Tartışmalar

Alanın hızlı gelişimi beraberinde önemli soruları getirmiştir:

- **Önyargı ve ayrımcılık:** Eğitim verisindeki yanlılıkların kararlara yansıması.
- **Mahremiyet:** Kişisel verilerin toplanması ve işlenmesi.
- **İstihdam:** Otomasyonun iş gücü piyasasına etkileri.
- **Hesap verebilirlik:** Otonom sistemlerin kararlarından kimin sorumlu olduğu.
- **Uzun vadeli riskler:** Genel yapay zekânın kontrolüne ilişkin tartışmalar.

Avrupa Birliği'nin **Yapay Zekâ Yasası** gibi düzenlemeler, riske dayalı bir denetim çerçevesi oluşturma çabasının örnekleridir.

## Türkiye'de Yapay Zekâ

Türkiye, 2021'de **Ulusal Yapay Zekâ Stratejisi**'ni yayımlamış; üniversitelerde yapay zekâ mühendisliği bölümleri açılmış, kamu ve özel sektörde uygulama projeleri hız kazanmıştır. Türkçe doğal dil işleme, dilin eklemeli yapısı nedeniyle alanın özgün araştırma konularından biridir.
