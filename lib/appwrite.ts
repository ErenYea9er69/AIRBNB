import {
  Client,
  Account,
  ID,
  Databases,
  OAuthProvider,
  Avatars,
  Query,
  Storage,
} from "appwrite";

export const config = {
  endpoint: process.env.NEXT_PUBLIC_APPWRITE_ENDPOINT,
  projectId: process.env.NEXT_PUBLIC_APPWRITE_PROJECT_ID,
  databaseId: process.env.NEXT_PUBLIC_APPWRITE_DATABASE_ID,
  galleriesCollectionId:
    process.env.NEXT_PUBLIC_APPWRITE_GALLERIES_COLLECTION_ID,
  reviewsCollectionId: process.env.NEXT_PUBLIC_APPWRITE_REVIEWS_COLLECTION_ID,
  agentsCollectionId: process.env.NEXT_PUBLIC_APPWRITE_AGENTS_COLLECTION_ID,
  propertiesCollectionId:
    process.env.NEXT_PUBLIC_APPWRITE_PROPERTIES_COLLECTION_ID,
  bucketId: process.env.NEXT_PUBLIC_APPWRITE_BUCKET_ID,
};

export const client = new Client();
client
  .setEndpoint(config.endpoint!)
  .setProject(config.projectId!);

export const avatar = new Avatars(client);
export const account = new Account(client);
export const databases = new Databases(client);
export const storage = new Storage(client);

export async function loginWithEmail({ email, password }: { email: string, password: string }) {
  try {
    const session = await account.createEmailPasswordSession(email, password);
    return !!session;
  } catch (error) {
    console.error(error);
    return false;
  }
}

export async function register({ email, password, name }: { email: string, password: string, name: string }) {
  try {
    const user = await account.create(ID.unique(), email, password, name);
    if (user) {
      // Automatically login after registration
      return await loginWithEmail({ email, password });
    }
    return false;
  } catch (error) {
    console.error(error);
    return false;
  }
}

export async function logout() {
  try {
    const result = await account.deleteSession("current");
    return result;
  } catch (error) {
    console.error(error);
    return false;
  }
}

export async function getCurrentUser() {
  try {
    const result = await account.get();
    if (result.$id) {
      const userAvatar = avatar.getInitials(result.name);

      return {
        ...result,
        avatar: userAvatar.toString(),
      };
    }

    return null;
  } catch (error) {
    console.log(error);
    return null;
  }
}

export async function getLatestProperties() {
  try {
    const result = await databases.listDocuments(
      config.databaseId!,
      config.propertiesCollectionId!,
      [Query.orderAsc("$createdAt"), Query.limit(5)]
    );

    return result.documents;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getProperties({
  filter,
  query,
  limit,
}: {
  filter: string;
  query: string;
  limit?: number;
}) {
  try {
    const buildQuery = [Query.orderDesc("$createdAt")];

    if (filter && filter !== "All")
      buildQuery.push(Query.equal("type", filter));

    if (query)
      buildQuery.push(
        Query.or([
          Query.search("name", query),
          Query.search("address", query),
          Query.search("type", query),
        ])
      );

    if (limit) buildQuery.push(Query.limit(limit));

    const result = await databases.listDocuments(
      config.databaseId!,
      config.propertiesCollectionId!,
      buildQuery
    );

    return result.documents;
  } catch (error) {
    console.error(error);
    return [];
  }
}

export async function getPropertyById({ id }: { id: string }) {
  try {
    const result = await databases.getDocument(
      config.databaseId!,
      config.propertiesCollectionId!,
      id
    );
    return result;
  } catch (error) {
    console.error(error);
    return null;
  }
}
